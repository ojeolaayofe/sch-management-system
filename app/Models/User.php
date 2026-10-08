<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarUrl(int $size = 40): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=' . $size . '&background=random';
    }

    /**
     * Get the teacher profile for the user.
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }
}
