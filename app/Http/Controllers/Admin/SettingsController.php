<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $siteSettings = Setting::where('group', 'site')->get()->keyBy('key');
        $securitySettings = Setting::where('group', 'security')->get()->keyBy('key');

        return view('admin.settings.index', compact('siteSettings', 'securitySettings'));
    }

    public function updateSite(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'site_url' => 'nullable|url|max:255',
            'admin_email' => 'nullable|email|max:255',
            'support_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'timezone' => 'required|string|max:100',
            'date_format' => 'required|string|max:50',
            'time_format' => 'required|string|max:50',
            'language' => 'required|string|max:10',
        ]);

        foreach ($validated as $key => $value) {
            Setting::where('key', $key)->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string', 'group' => 'site']
            );
        }

        return back()->with('success', 'Site settings updated successfully.');
    }

    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'password_min_length' => 'required|integer|min:6|max:128',
            'password_require_uppercase' => 'boolean',
            'password_require_lowercase' => 'boolean',
            'password_require_numbers' => 'boolean',
            'password_require_special' => 'boolean',
            'session_lifetime' => 'required|integer|min:10|max:525600',
            'two_factor_enabled' => 'boolean',
            'max_login_attempts' => 'required|integer|min:1|max:100',
            'lockout_duration' => 'required|integer|min:1|max:1440',
            'enable_rate_limiting' => 'boolean',
            'rate_limit_max_attempts' => 'required|integer|min:1|max:1000',
            'rate_limit_decay_minutes' => 'required|integer|min:1|max:120',
            'enable_hsts' => 'boolean',
            'enable_csp' => 'boolean',
            'enable_x_frame_options' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $type = $setting->type;
                if ($type === 'boolean') {
                    $value = $value ? '1' : '0';
                }
                $setting->update(['value' => (string) $value]);
            }
        }

        return back()->with('success', 'Security settings updated successfully.');
    }
}
