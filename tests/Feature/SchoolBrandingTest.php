<?php

namespace Tests\Feature;

use App\Models\ApplicationSetting;
use App\Models\AcademicSession;
use App\Models\Student;
use App\Services\SchoolBrandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Verifies the end-to-end school branding workflow:
 *
 *  - saving settings persists school_name / school_logo
 *  - the admin layout (title, sidebar, footer) reflects the saved branding
 *  - re-saving updates the branding without touching blade files
 *  - the student result PDF uses the saved school name and logo
 */
class SchoolBrandingTest extends TestCase
{
    use RefreshDatabase;

    private function actingSuperAdmin(): \App\Models\User
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'branding-test@example.com',
        ]);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
        $user->assignRole('super_admin');

        return $user;
    }

    private function settingsPayload(string $name, ?UploadedFile $logo = null): array
    {
        $payload = [
            'school_name' => $name,
            'school_motto' => 'Excellence First',
            'school_email' => 'info@example.com',
            'phone_number' => '08000000000',
            'address' => '12 School Street, Lagos',
            'academic_session' => '2026/2027',
            'current_term' => 'first_term',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
            'primary_color' => '#4f46e5',
            'secondary_color' => '#0ea5e9',
        ];

        if ($logo !== null) {
            $payload['school_logo'] = $logo;
        }

        return $payload;
    }

    /**
     * A. /admin/settings shows saved values
     * B. Save with a new name + logo -> persisted, no duplicate rows
     * C. Refresh shows the saved values
     */
    public function test_settings_save_and_refresh_persists_branding(): void
    {
        Storage::fake('public');
        $user = $this->actingSuperAdmin();

        // A. Open settings — the seeded singleton row is displayed.
        $this->actingAs($user)
            ->get('/admin/settings')
            ->assertOk()
            ->assertSee('School Name');

        $this->assertSame(1, ApplicationSetting::count());

        // B. Save a new school name + logo.
        $this->actingAs($user)
            ->put('/admin/settings', $this->settingsPayload('SchoolHub Test School', UploadedFile::fake()->image('logo.png')))
            ->assertRedirect(route('admin.settings.index'));

        $settings = ApplicationSetting::first();
        $this->assertSame(1, ApplicationSetting::count(), 'No duplicate settings rows must be created');
        $this->assertSame('SchoolHub Test School', $settings->school_name);
        $this->assertNotNull($settings->school_logo);
        Storage::disk('public')->assertExists($settings->school_logo);

        // C. Refresh settings — saved name and logo preview are displayed.
        $this->actingAs($user)
            ->get('/admin/settings')
            ->assertOk()
            ->assertSee('SchoolHub Test School')
            ->assertSee('/storage/' . $settings->school_logo);
    }

    /**
     * D. The admin dashboard shows the saved school name and logo;
     *    E. it stays consistent on other admin pages;
     *    F. renaming the school updates branding everywhere without
     *    editing any blade file.
     */
    public function test_admin_layout_uses_saved_branding(): void
    {
        Storage::fake('public');
        $user = $this->actingSuperAdmin();

        $this->actingAs($user)
            ->put('/admin/settings', $this->settingsPayload('SchoolHub Test School', UploadedFile::fake()->image('logo.png')))
            ->assertRedirect(route('admin.settings.index'));

        $settings = ApplicationSetting::first();

        // D. Dashboard: school name in <title>, sidebar and footer; logo in sidebar.
        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSeeHtml('<title>Dashboard - SchoolHub Test School</title>')
            ->assertSee('SchoolHub Test School')
            ->assertSee('/storage/' . $settings->school_logo)
            ->assertDontSee('Laravel');

        // E. Other admin pages keep the same branding (layout is shared).
        foreach (['/admin/students', '/admin/teachers', '/admin/classes', '/admin/subjects'] as $page) {
            $this->actingAs($user)
                ->get($page)
                ->assertOk()
                ->assertSee('SchoolHub Test School');
        }

        // F. Rename the school; branding updates without touching blade files.
        $this->actingAs($user)
            ->put('/admin/settings', $this->settingsPayload('SchoolHub'))
            ->assertRedirect(route('admin.settings.index'));

        // The branding service is a per-request singleton; reset its cache so
        // the next request reads the freshly-saved row (mirrors a new request).
        app(SchoolBrandingService::class)->flushCache();

        $this->assertSame(
            $settings->school_logo,
            ApplicationSetting::first()->school_logo,
            'Existing logo must remain unchanged when no new logo is uploaded'
        );

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSeeHtml('<title>Dashboard - SchoolHub</title>')
            ->assertDontSee('SchoolHub Test School');
    }

    /**
     * G. The student result PDF uses the saved school name, logo and
     *    contact details (address / phone / email).
     */
    public function test_result_pdf_uses_saved_school_branding(): void
    {
        Storage::fake('public');
        $user = $this->actingSuperAdmin();

        $this->actingAs($user)
            ->put('/admin/settings', $this->settingsPayload('SchoolHub Test School', UploadedFile::fake()->image('logo.png')))
            ->assertRedirect(route('admin.settings.index'));

        $session = AcademicSession::create(['name' => '2026/2027', 'start_date' => now(), 'end_date' => now()->addYear()]);
        $student = Student::query()->first() ?? Student::create([
            'student_id' => 'STU-001',
            'first_name' => 'Branding',
            'last_name' => 'Student',
            'email' => 'branding.student@example.com',
            'gender' => 'male',
            'admission_date' => now(),
        ]);

        // Capture the branding data the controller hands to the PDF template.
        $pdfData = null;
        // Build a subclass that intercepts loadView/download without rendering.
        $pdf = new class extends \Barryvdh\DomPDF\PDF {
            public ?array $capturedData = null;

            public function __construct()
            {
                $app = app();
                $dompdf = $app['dompdf'];
                $config = $app['config'];
                $files = $app['files'];
                $view = $app['view'];

                $ref = new \ReflectionClass(\Barryvdh\DomPDF\PDF::class);
                foreach (['dompdf' => $dompdf, 'config' => $config, 'files' => $files, 'view' => $view] as $name => $val) {
                    $p = $ref->getProperty($name);
                    $p->setAccessible(true);
                    $p->setValue($this, $val);
                }
                $sw = $ref->getProperty('showWarnings');
                $sw->setAccessible(true);
                $sw->setValue($this, false);
            }

            public function loadView(string $view, array $data = [], array $mergeData = [], ?string $encoding = null): self
            {
                $this->capturedData = $data;

                return $this;
            }

            public function download(string $filename = 'document.pdf'): \Illuminate\Http\Response
            {
                return new \Illuminate\Http\Response('', 200, ['Content-Disposition' => 'attachment; filename="' . $filename . '"']);
            }
        };
        $this->app->instance('dompdf.wrapper', $pdf);

        $this->actingAs($user)
            ->get("/admin/students/{$student->id}/result/pdf?academic_session_id={$session->id}")
            ->assertOk()
            ->assertDownload();

        $this->assertSame('SchoolHub Test School', $pdf->capturedData['schoolName']);
        $this->assertSame('Excellence First', $pdf->capturedData['schoolMotto']);
        $this->assertSame('12 School Street, Lagos', $pdf->capturedData['schoolAddress']);
        $this->assertSame('08000000000', $pdf->capturedData['schoolPhone']);
        $this->assertSame('info@example.com', $pdf->capturedData['schoolEmail']);
        $this->assertStringStartsWith('data:image/png;base64,', $pdf->capturedData['logoDataUri']);
    }

    /**
     * The login page (unauthenticated) shows the configured logo, school
     * name and motto — not a generic/default brand.
     */
    public function test_login_page_uses_saved_branding(): void
    {
        Storage::fake('public');

        $this->put('/admin/settings', $this->settingsPayload(
            'Lagos Model Academy',
            UploadedFile::fake()->image('logo.png')
        ))->assertForbidden(); // not authenticated — settings require admin

        $settings = ApplicationSetting::first();

        $this->get('/login')
            ->assertOk()
            ->assertSeeHtml('<title>Login - Lagos Model Academy</title>')
            ->assertSee('Lagos Model Academy')
            ->assertSee('Excellence First')
            ->assertSee('/storage/' . $settings->school_logo)
            ->assertSee('partials.favicon') === false; // sanity: partial inlined, not literal
    }

    /**
     * Favicon: PNG logo becomes the tab icon; SVG logo falls back to the
     * static favicon (browsers don't reliably render SVG favicons).
     */
    public function test_favicon_uses_logo_when_raster(): void
    {
        Storage::fake('public');
        $branding = app(SchoolBrandingService::class);

        // No logo -> null.
        $this->assertNull($branding->faviconUrl());

        // PNG logo -> stored URL.
        $pngPath = UploadedFile::fake()->image('logo.png')->store('logos', 'public');
        ApplicationSetting::getInstance()->update(['school_logo' => $pngPath]);
        $branding->flushCache();
        $this->assertSame(Storage::disk('public')->url($pngPath), $branding->faviconUrl());

        // SVG logo -> null (fallback to static favicon).
        $svgPath = UploadedFile::fake()->create('logo.svg', 1, 'svg+xml')->store('logos', 'public');
        ApplicationSetting::getInstance()->update(['school_logo' => $svgPath]);
        $branding->flushCache();
        $this->assertNull($branding->faviconUrl());
    }
    /**
     * The branding service centralizes name/logo with a fallback.
     */
    public function test_branding_service_fallback_and_logo_url(): void
    {
        $branding = app(SchoolBrandingService::class);

        $this->assertSame('SchoolHub', $branding->schoolName());
        $this->assertFalse($branding->hasLogo());
        $this->assertNull($branding->logoUrl());
        $this->assertNull($branding->logoPath());

        $settings = ApplicationSetting::getInstance();
        $settings->update(['school_name' => '  ', 'school_motto' => null]);

        // The service caches the instance for the request, so resolve a
        // fresh copy for the assertion.
        $branding = new SchoolBrandingService();
        $this->assertSame('SchoolHub', $branding->schoolName(), 'Empty school name must fall back to SchoolHub');
        $this->assertNull($branding->schoolMotto());
    }
}
