<?php

use App\Services\SchoolBrandingService;

if (! function_exists('schoolName')) {
    /**
     * The saved school name, with a fallback to "SchoolHub" when missing.
     */
    function schoolName(): string
    {
        return app(SchoolBrandingService::class)->schoolName();
    }
}

if (! function_exists('schoolBrand')) {
    /**
     * The shared SchoolBrandingService instance.
     */
    function schoolBrand(): SchoolBrandingService
    {
        return app(SchoolBrandingService::class);
    }
}
