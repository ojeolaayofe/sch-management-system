<?php

namespace App\Services;

/**
 * Application Settings Service
 * 
 * Handles all settings-related business logic.
 * 
 * @package SchoolHub\Services
 */
class SettingsService
{
    /**
     * Update application settings
     * 
     * @param array<string, mixed> $data
     * @return \App\Models\ApplicationSetting
     */
    public function update(array $data): \App\Models\ApplicationSetting
    {
        $settings = \App\Models\ApplicationSetting::getInstance();
        
        // Handle logo upload
        if (isset($data['school_logo'])) {
            $logoPath = $data['school_logo']->store('logos', 'public');
            $data['school_logo'] = $logoPath;
        }
        
        $settings->update([
            'school_name' => $data['school_name'] ?? $settings->school_name,
            'school_logo' => $data['school_logo'] ?? $settings->school_logo,
            'school_email' => $data['school_email'] ?? $settings->school_email,
            'phone_number' => $data['phone_number'] ?? $settings->phone_number,
            'address' => $data['address'] ?? $settings->address,
            'academic_session' => $data['academic_session'] ?? $settings->academic_session,
            'current_term' => $data['current_term'] ?? $settings->current_term,
            'timezone' => $data['timezone'] ?? $settings->timezone,
            'currency' => $data['currency'] ?? $settings->currency,
            'school_motto' => $data['school_motto'] ?? $settings->school_motto,
            'primary_color' => $data['primary_color'] ?? $settings->primary_color,
            'secondary_color' => $data['secondary_color'] ?? $settings->secondary_color,
            'smtp_host' => $data['smtp_host'] ?? $settings->smtp_host,
            'smtp_port' => $data['smtp_port'] ?? $settings->smtp_port,
            'smtp_username' => $data['smtp_username'] ?? $settings->smtp_username,
            'smtp_password' => $data['smtp_password'] ?? $settings->smtp_password,
            'smtp_encryption' => $data['smtp_encryption'] ?? $settings->smtp_encryption,
        ]);
        
        return $settings;
    }
    
    /**
     * Get all settings
     * 
     * @return \App\Models\ApplicationSetting
     */
    public function getAll(): \App\Models\ApplicationSetting
    {
        return \App\Models\ApplicationSetting::getInstance();
    }
}
