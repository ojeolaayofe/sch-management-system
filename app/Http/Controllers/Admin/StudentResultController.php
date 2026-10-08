<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SchoolBrandingService;
use App\Models\Student;
use App\Services\StudentResultService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Student Result PDF Controller
 *
 * Generates a printable A4 result sheet for a student within a selected
 * academic session and (optionally) term. Restricted to super_admin and
 * school_administrator via the surrounding route middleware + policy.
 *
 * @package SchoolHub\Http\Controllers\Admin
 */
class StudentResultController extends Controller
{
    public function __construct(private StudentResultService $service)
    {
    }

    /**
     * Generate and stream/download the student result PDF.
     */
    public function pdf(Request $request, int $student)
    {
        $user = $request->user();

        // Authorization: only super_admin and school_administrator may
        // generate student results. This mirrors the StudentPolicy::viewAny
        // rule and keeps the feature out of teacher/student reach.
        abort_unless(
            $user->hasRole('super_admin') || $user->hasRole('school_administrator'),
            403,
            'You are not authorized to generate student results.'
        );

        $studentModel = Student::findOrFail($student);

        $validated = $request->validate([
            'academic_session_id' => 'required|integer|exists:academic_sessions,id',
            'academic_term_id' => 'nullable|integer|exists:academic_terms,id',
        ]);

        $sessionId = (int) $validated['academic_session_id'];
        $termId = isset($validated['academic_term_id']) && $validated['academic_term_id'] !== ''
            ? (int) $validated['academic_term_id']
            : null;

        $result = $this->service->buildResult($studentModel, $sessionId, $termId);

        $branding = app(SchoolBrandingService::class);

        $pdfData = [
            'result' => $result,
            'schoolName' => $branding->schoolName(),
            'schoolMotto' => $branding->schoolMotto(),
            'schoolAddress' => $branding->address(),
            'schoolPhone' => $branding->phone(),
            'schoolEmail' => $branding->email(),
            'logoPath' => $branding->logoPath(),
        ];

        // DomPDF cannot fetch remote URLs (and network access is often
        // disabled), so convert the saved logo to a data URI that the
        // blade template can embed directly.
        $logoPath = $pdfData['logoPath'];

        if ($logoPath && ! str_starts_with($logoPath, 'http') && is_file($logoPath)) {
            $mime = match (strtolower(pathinfo($logoPath, PATHINFO_EXTENSION))) {
                'svg' => 'image/svg+xml',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                default => 'image/png',
            };

            $pdfData['logoDataUri'] = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.students.result-pdf', $pdfData)
            ->setPaper('a4', 'portrait');

        // Default to download so the browser saves the file.
        $filename = sprintf(
            'result-%s-%s.pdf',
            $studentModel->student_id,
            $termId ? 'term-' . $termId : 'all-terms'
        );

        return $pdf->download($filename);
    }
}
