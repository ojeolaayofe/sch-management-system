<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Teacher Subject Assignment Model
 *
 * teacher_subject_assignments.teacher_id -> users.id
 */
class TeacherSubjectAssignment extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'class_arm_id',
        'academic_session_id',
        'status',
    ];

    /**
     * Get the user account for this assignment.
     * teacher_id references users.id (not teachers.id).
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the subject for this assignment.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class arm for this assignment.
     */
    public function classArm(): BelongsTo
    {
        return $this->belongsTo(ClassArm::class);
    }

    /**
     * Get the academic session for this assignment.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Scope for active assignments.
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', 'active');
    }
}
