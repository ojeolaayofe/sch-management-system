<?php

namespace App\Models;

/**
 * Academic Term Model
 *
 * @package SchoolHub\Models
 *
 * @property int $id
 * @property int $academic_session_id
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 * @property bool $is_current
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Relations\BelongsTo $academicSession
 */
class AcademicTerm extends \Illuminate\Database\Eloquent\Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * Fillable attributes
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'academic_session_id',
        'name',
        'start_date',
        'end_date',
        'is_current',
        'status',
    ];

    /**
     * Cast attributes
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Boot the model
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($term) {
            if ($term->is_current) {
                static::where('is_current', true)->update(['is_current' => false]);
            }
        });

        static::updating(function ($term) {
            if ($term->is_current && $term->isDirty('is_current')) {
                static::where('id', '!=', $term->id)->where('is_current', true)->update(['is_current' => false]);
            }
        });
    }

    /**
     * Get the academic session for this term
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicSession(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the current term
     *
     * @return static|null
     */
    public static function getCurrent(): ?static
    {
        return static::where('is_current', true)->first();
    }

    /**
     * Get human-readable term name
     *
     * @return string
     */
    public function getLabelAttribute(): string
    {
        return match ($this->name) {
            'first_term' => 'First Term',
            'second_term' => 'Second Term',
            'third_term' => 'Third Term',
            'annual' => 'Annual',
            default => $this->name,
        };
    }

    /**
     * Scope for active terms
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', 'active');
    }
}
