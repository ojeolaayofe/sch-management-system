<?php

namespace App\Models;

/**
 * Class Model
 *
 * @package SchoolHub\Models
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $section
 * @property int $display_order
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Relations\HasMany $arms
 */
class ClassModel extends \Illuminate\Database\Eloquent\Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'classes';

    /**
     * Fillable attributes
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'section',
        'display_order',
        'status',
    ];

    /**
     * Get the arms for this class
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function arms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClassArm::class);
    }

    /**
     * Get section label
     *
     * @return string
     */
    public function getSectionLabelAttribute(): string
    {
        return match ($this->section) {
            'nursery' => 'Nursery',
            'primary' => 'Primary',
            'junior_secondary' => 'Junior Secondary',
            'senior_secondary' => 'Senior Secondary',
            default => $this->section,
        };
    }

    /**
     * Scope for active classes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for section
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $section
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySection(\Illuminate\Database\Eloquent\Builder $query, string $section): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('section', $section);
    }
}
