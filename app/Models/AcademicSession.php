<?php

namespace App\Models;

/**
 * Academic Session Model
 *
 * @package SchoolHub\Models
 *
 * @property int $id
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 * @property bool $is_current
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Relations\HasMany $terms
 */
class AcademicSession extends \Illuminate\Database\Eloquent\Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * Fillable attributes
     *
     * @var array<int, string>
     */
    protected $fillable = [
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

        static::creating(function ($session) {
            if ($session->is_current) {
                static::where('is_current', true)->update(['is_current' => false]);
            }
        });

        static::updating(function ($session) {
            if ($session->is_current && $session->isDirty('is_current')) {
                static::where('id', '!=', $session->id)->where('is_current', true)->update(['is_current' => false]);
            }
        });
    }

    /**
     * Get the terms for this session
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function terms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcademicTerm::class);
    }

    /**
     * Get the current session
     *
     * @return static|null
     */
    public static function getCurrent(): ?static
    {
        return static::where('is_current', true)->first();
    }

    /**
     * Scope for active sessions
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', 'active');
    }
}
