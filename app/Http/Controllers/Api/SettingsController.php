<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function savePreferences(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);

        $data = $request->validate([
            'theme' => 'sometimes|string|in:light,dark',
            'notification_enabled' => 'sometimes|boolean',
            'email_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
        ]);

        UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => $data['theme'] ?? 'light',
                'notification_enabled' => $data['notification_enabled'] ?? true,
                'email_notifications' => $data['email_notifications'] ?? true,
                'sms_notifications' => $data['sms_notifications'] ?? false,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Preferences saved successfully.']);
    }
}
