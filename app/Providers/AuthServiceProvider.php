<?php

namespace App\Providers;

/**
 * Auth Service Provider
 *
 * @package SchoolHub\Providers
 */
class AuthServiceProvider extends \Illuminate\Foundation\Support\Providers\AuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\AcademicSession::class => \App\Policies\AcademicSessionPolicy::class,
        \App\Models\AcademicTerm::class => \App\Policies\AcademicTermPolicy::class,
        \App\Models\ClassModel::class => \App\Policies\ClassPolicy::class,
        \App\Models\ClassArm::class => \App\Policies\ClassArmPolicy::class,
        \App\Models\Subject::class => \App\Policies\SubjectPolicy::class,
        \App\Models\Teacher::class => \App\Policies\TeacherPolicy::class,
        \App\Models\Student::class => \App\Policies\StudentPolicy::class,
        \App\Models\Attendance::class => \App\Policies\AttendancePolicy::class,
        \App\Models\TeacherSubjectAssignment::class => \App\Policies\TeacherSubjectAssignmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
