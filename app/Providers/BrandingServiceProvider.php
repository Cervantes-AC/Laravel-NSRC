<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class BrandingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        try {
            $siteName = Setting::getValue('site_name');
            if ($siteName) {
                config(['app.name' => $siteName]);
            }

            $logo = Setting::getValue('branding_logo');
            if ($logo) {
                config(['app.logo' => 'storage/' . $logo]);
            }

            $favicon = Setting::getValue('branding_favicon');
            if ($favicon) {
                config(['app.favicon' => 'storage/' . $favicon]);
            }
        } catch (\Exception $e) {
            // Database may not be ready (e.g., during migrations, fresh install)
        }
    }
}
