<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Application Setting Model
 *
 * Singleton model representing the application's global settings.
 * Always uses the row with id = 1.
 *
 * @package SchoolHub\Models
 */
class ApplicationSetting extends Model
{
    protected $table = 'application_settings';

    protected $fillable = [
        'school_name',
        'school_motto',
        'school_email',
        'phone_number',
        'address',
        'school_logo',
        'academic_session',
        'current_term',
        'timezone',
        'currency',
        'primary_color',
        'secondary_color',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
    ];

    /**
     * Get the singleton settings instance.
     * Creates it if it doesn't exist.
     */
    public static function getInstance(): self
    {
        $settings = static::first();

        if (!$settings) {
            $settings = static::create([
                'school_name' => 'SchoolHub',
                'timezone' => config('app.timezone', 'Africa/Lagos'),
                'currency' => 'NGN',
                'primary_color' => '#4f46e5',
                'secondary_color' => '#0ea5e9',
                'smtp_encryption' => 'tls',
            ]);
        }

        return $settings;
    }
}
