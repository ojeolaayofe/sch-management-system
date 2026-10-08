<?php

namespace App\Models;

/**
 * Subject Model
 *
 * @package SchoolHub\Models
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $category
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Relations\HasMany $teacherSubjectAssignments
 */
class Subject extends \Illuminate\Database\Eloquent\Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * Fillable attributes
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'category',
        'status',
    ];

    /**
     * Get teacher subject assignments for this subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teacherSubjectAssignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeacherSubjectAssignment::class);
    }

    /**
     * Get category label
     *
     * @return string
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'core' => 'Core',
            'elective' => 'Elective',
            'science' => 'Science',
            'arts' => 'Arts',
            'commercial' => 'Commercial',
            'technical' => 'Technical',
            default => $this->category,
        };
    }

    /**
     * Scope for active subjects
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for category
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory(\Illuminate\Database\Eloquent\Builder $query, string $category): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('category', $category);
    }
}
