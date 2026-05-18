<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $qrCode = null;

        // Generate QR code if 2FA is not enabled
        if (!$user->two_factor_enabled) {
            $google2fa = new Google2FA();
            $secret = $google2fa->generateSecretKey();
            $qrCode = $google2fa->getQRCodeInline(
                config('app.name'),
                $user->email,
                $secret
            );
        }

        return view('profile.edit', [
            'user' => $user,
            'qrCode' => $qrCode,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['avatar']);

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            if ($request->user()->avatar) {
                Storage::disk('public')->delete($request->user()->avatar);
            }

            $request->user()->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Enable two-factor authentication.
     */
    public function enableTwoFactor(Request $request): RedirectResponse
    {
        $request->validate([
            'totp_code' => ['required', 'string', 'size:6'],
        ]);

        $google2fa = new Google2FA();
        $secret = session('two_factor_secret');

        if (!$secret) {
            $secret = $google2fa->generateSecretKey();
        }

        // Verify the TOTP code
        if (!$google2fa->verifyKey($secret, $request->totp_code)) {
            return Redirect::route('profile.edit')->withErrors([
                'totp_code' => __('The provided code is invalid.'),
            ]);
        }

        // Generate backup codes
        $backupCodes = $this->generateBackupCodes();

        $request->user()->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => $secret,
            'two_factor_backup_codes' => json_encode($backupCodes),
        ]);

        session()->forget('two_factor_secret');

        return Redirect::route('profile.edit')->with('status', 'two-factor-enabled');
    }

    /**
     * Disable two-factor authentication.
     */
    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->user()->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_backup_codes' => null,
        ]);

        return Redirect::route('profile.edit')->with('status', 'two-factor-disabled');
    }

    /**
     * Generate backup codes.
     */
    private function generateBackupCodes(int $count = 10): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return $codes;
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
