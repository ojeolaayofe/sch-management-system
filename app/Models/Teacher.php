<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Teacher extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'teacher_id', 'first_name', 'middle_name', 'last_name',
        'email', 'phone', 'address', 'passport_photo', 'qualification',
        'employment_date', 'status',
    ];

    protected $casts = [
        'employment_date' => 'date',
    ];

    /**
     * Get the user account that owns this teacher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject assignments for this teacher.
     *
     * teacher_subject_assignments.teacher_id references users.id,
     * so we route through the User relationship.
     */
    public function teacherSubjectAssignments(): HasManyThrough
    {
        return $this->hasManyThrough(
            TeacherSubjectAssignment::class,
            User::class,
            'id',       // User key in teachers (user_id)
            'teacher_id', // TeacherSubjectAssignment key in users
            'user_id',  // Foreign key on teachers table
            'id'        // Local key on TeacherSubjectAssignment table
        );
    }

    /**
     * Get the attendance records for this teacher.
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the assessment scores recorded by this teacher.
     */
    public function assessmentScores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class);
    }

    /**
     * Get the examination scores recorded by this teacher.
     */
    public function examinationScores(): HasMany
    {
        return $this->hasMany(ExaminationScore::class);
    }

    /**
     * Get the result remarks by this teacher.
     */
    public function resultRemarks(): HasMany
    {
        return $this->hasMany(ResultRemark::class);
    }

    /**
     * Get the full name of the teacher.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ?? '') . ' ' . $this->last_name);
    }

    /**
     * Generate the next sequential teacher ID.
     */
    public static function generateTeacherId(): string
    {
        $year = date('Y');
        $lastTeacher = self::where('teacher_id', 'like', "TCH-{$year}-%")->latest('teacher_id')->first();
        $lastNumber = $lastTeacher ? intval(substr($lastTeacher->teacher_id, -4)) : 0;
        $newNumber = str_pad(strval($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        return "TCH-{$year}-{$newNumber}";
    }
}
