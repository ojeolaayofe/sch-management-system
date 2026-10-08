<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'passport_photo',
        'phone',
        'email',
        'address',
        'admission_date',
        'status',
        'parent_guardian_name',
        'parent_guardian_phone',
        'parent_guardian_email',
        'parent_guardian_relationship',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
    ];

    /**
     * Get the student's class assignments.
     */
    public function classAssignments(): HasMany
    {
        return $this->hasMany(StudentClassAssignment::class);
    }

    /**
     * Get the student's current class assignment.
     */
    public function currentClassAssignment()
    {
        return $this->classAssignments()->where('status', 'active')->latest()->first();
    }

    /**
     * Get the student's attendance records.
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the student's assessment scores.
     */
    public function assessmentScores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class);
    }

    /**
     * Get the student's examination scores.
     */
    public function examinationScores(): HasMany
    {
        return $this->hasMany(ExaminationScore::class);
    }

    /**
     * Get the student's result remarks.
     */
    public function resultRemarks(): HasMany
    {
        return $this->hasMany(ResultRemark::class);
    }

    /**
     * Get the full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ?? '') . ' ' . $this->last_name);
    }

    /**
     * Generate a unique student ID.
     */
    public static function generateStudentId(): string
    {
        $year = date('Y');
        $lastStudent = self::where('student_id', 'like', "SH-{$year}-%")->latest('student_id')->first();
        
        if ($lastStudent) {
            $lastNumber = intval(substr($lastStudent->student_id, -4));
            $newNumber = str_pad(strval($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "SH-{$year}-{$newNumber}";
    }
}
