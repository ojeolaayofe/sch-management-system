<?php

namespace App\Models;

/**
 * Class Arm Model
 *
 * @package SchoolHub\Models
 *
 * @property int $id
 * @property int $class_id
 * @property string $arm_name
 * @property int $capacity
 * @property int|null $class_teacher_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Relations\BelongsTo $class
 * @property \Illuminate\Database\Eloquent\Relations\BelongsTo $classTeacher
 * @property \Illuminate\Database\Eloquent\Relations\HasMany $teacherSubjectAssignments
 */
class ClassArm extends \Illuminate\Database\Eloquent\Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * Fillable attributes
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_id',
        'arm_name',
        'capacity',
        'class_teacher_id',
        'status',
    ];

    /**
     * Cast attributes
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'integer',
    ];

    /**
     * Get the class for this arm
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ClassModel::class);
    }

    /**
     * Get the class teacher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classTeacher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    /**
     * Get teacher subject assignments for this arm
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teacherSubjectAssignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeacherSubjectAssignment::class);
    }

    /**
     * Scope for active arms
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', 'active');
    }
}
