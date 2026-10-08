<?php

namespace App\Services;

use App\Models\ApplicationSetting;
use Illuminate\Support\Facades\Storage;

/**
 * School Branding Service
 *
 * Single source of truth for the current school's branding (name, motto,
 * logo) used across the admin UI and generated documents (PDFs, etc.).
 *
 * @package SchoolHub\Services
 */
class SchoolBrandingService
{
    /** @var ApplicationSetting|null */
    private ?ApplicationSetting $settings = null;

    /**
     * The active ApplicationSetting (singleton row), cached for the request.
     */
    public function settings(): ApplicationSetting
    {
        return $this->settings ??= ApplicationSetting::getInstance();
    }

    /**
     * Forget the cached settings instance (used in tests after saving).
     */
    public function flushCache(): void
    {
        $this->settings = null;
    }

    /**
     * The current school name with a sensible fallback.
     */
    public function schoolName(): string
    {
        return trim((string) $this->settings()->school_name) ?: 'SchoolHub';
    }

    /**
     * The current school motto (may be empty).
     */
    public function schoolMotto(): ?string
    {
        return $this->settings()->school_motto ?: null;
    }

    /**
     * The current school address (may be empty).
     */
    public function address(): ?string
    {
        return $this->settings()->address ?: null;
    }

    /**
     * The current school phone number (may be empty).
     */
    public function phone(): ?string
    {
        return $this->settings()->phone_number ?: null;
    }

    /**
     * The current school email (may be empty).
     */
    public function email(): ?string
    {
        return $this->settings()->school_email ?: null;
    }

    /**
     * True if a logo path is present on the settings row.
     */
    public function hasLogo(): bool
    {
        return (bool) $this->settings()->school_logo;
    }

    /**
     * A web-accessible URL for the current logo (or null when absent).
     *
     * Handles both absolute http(s) URLs and relative paths stored on the
     * "public" disk (compatible with the public/storage symlink).
     */
    public function logoUrl(): ?string
    {
        $logo = $this->settings()->school_logo;

        if (!$logo) {
            return null;
        }

        if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
            return $logo;
        }

        return Storage::disk('public')->url($logo);
    }

    /**
     * A URL suitable for use as the browser tab icon (favicon).
     *
     * Returns the stored logo URL only when it is a raster image format all
     * modern browsers accept as a favicon (PNG/JPG). Returns null otherwise
     * so callers can fall back to the static default favicon.
     */
    public function faviconUrl(): ?string
    {
        $url = $this->logoUrl();

        if (!$url) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;

        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg'], true)
            ? $url
            : null;
    }

    /**
     * Absolute filesystem path to the logo (for PDF libraries that read
     * from disk). Returns null when no logo is available or the file is
     * missing.
     */
    public function logoPath(): ?string
    {
        $logo = $this->settings()->school_logo;

        if (!$logo) {
            return null;
        }

        if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
            // Absolute URL — return as-is; consumers may fetch it themselves.
            return $logo;
        }

        $path = Storage::disk('public')->path($logo);

        return is_file($path) ? $path : null;
    }
}
