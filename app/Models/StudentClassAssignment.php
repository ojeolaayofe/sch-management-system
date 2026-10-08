<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentClassAssignment extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'academic_session_id', 'class_id', 'class_arm_id',
        'status', 'assigned_at', 'completed_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function classArm(): BelongsTo
    {
        return $this->belongsTo(ClassArm::class);
    }
}
