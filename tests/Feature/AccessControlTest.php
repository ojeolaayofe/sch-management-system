<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Verifies the account-creation and role-gating model:
 *
 *  - there is NO public self-registration (accounts are created by admins)
 *  - teachers are locked out of the admin console
 *  - role-less accounts cannot reach the admin console
 *  - super_admins and school_administrators can reach the admin dashboard
 */
class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(?string $role): User
    {
        $user = User::factory()->create();

        if ($role !== null) {
            Role::firstOrCreate(['name' => $role]);
            $user->assignRole($role);
        }

        return $user;
    }

    private function attachTeacherProfile(User $user): Teacher
    {
        return Teacher::create([
            'user_id' => $user->id,
            'teacher_id' => 'T-' . $user->id,
            'first_name' => 'Test',
            'last_name' => 'Teacher',
        ]);
    }

    public function test_public_registration_is_not_available(): void
    {
        // GET /register — the route must not exist.
        $this->get('/register')->assertNotFound();

        // POST /register — must not exist either.
        $this->post('/register', [
            'name' => 'Rogue',
            'email' => 'rogue@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertNotFound();

        // And it must not have created any account.
        $this->assertSame(0, User::where('email', 'rogue@example.com')->count());
    }

    public function test_teacher_cannot_access_admin_dashboard(): void
    {
        $user = $this->userWithRole('teacher');
        $this->attachTeacherProfile($user);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_roleless_user_cannot_access_admin_dashboard(): void
    {
        $user = $this->userWithRole(null);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $user = $this->userWithRole('super_admin');

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_school_administrator_can_access_admin_dashboard(): void
    {
        $user = $this->userWithRole('school_administrator');

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_teacher_can_access_their_own_dashboard(): void
    {
        $user = $this->userWithRole('teacher');
        $this->attachTeacherProfile($user);

        $this->actingAs($user)
            ->get('/teacher/dashboard')
            ->assertOk();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->get('/teacher/dashboard')->assertRedirect(route('login'));
    }
}
