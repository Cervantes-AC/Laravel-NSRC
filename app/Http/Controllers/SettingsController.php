<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsRequest;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function updateBranding(SettingsRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'branding'],
                ['value' => $value, 'type' => 'string']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Branding settings updated successfully.');
    }

    public function updateEmail(SettingsRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'email'],
                ['value' => $value, 'type' => 'string']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Email settings updated successfully.');
    }

    public function updateSecurity(SettingsRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'security'],
                ['value' => $value, 'type' => 'string']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Security settings updated successfully.');
    }

    public function updateBackup(SettingsRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'backup'],
                ['value' => $value, 'type' => 'string']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Backup settings updated successfully.');
    }

    public function updateNotifications(SettingsRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'notifications'],
                ['value' => $value, 'type' => 'string']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Notification settings updated successfully.');
    }
}
