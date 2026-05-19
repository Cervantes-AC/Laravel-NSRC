<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLoggedIn;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\MfaCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->two_factor_enabled) {
            Auth::logout();

            $request->session()->put('auth.two_factor', [
                'user_id' => $user->id,
                'remember' => $request->boolean('remember'),
                'expires_at' => now()->addMinutes(10)->timestamp,
            ]);

            $this->sendMfaCode($user);

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();
        $this->completeLogin($request, $user);

        return redirect()->intended(route($this->dashboardRoute($user), absolute: false));
    }

    public function twoFactorChallenge(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('auth.two_factor')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    public function verifyTwoFactor(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255'],
            'method' => ['nullable', 'in:totp,backup,email'],
        ]);

        $pending = $request->session()->get('auth.two_factor');

        if (! $pending || now()->timestamp > (int) $pending['expires_at']) {
            $request->session()->forget('auth.two_factor');

            return redirect()->route('login')->withErrors([
                'email' => 'The verification session expired. Please sign in again.',
            ]);
        }

        $user = User::find($pending['user_id']);
        if (! $user || ! $user->two_factor_enabled) {
            $request->session()->forget('auth.two_factor');

            return redirect()->route('login')->withErrors([
                'email' => 'Invalid verification session.',
            ]);
        }

        $method = $request->input('method', 'totp');
        $code = $request->string('code');

        $valid = match ($method) {
            'totp' => $this->verifyTotpCode($user, $code),
            'backup' => $this->verifyBackupCode($user, $code),
            'email' => $this->verifyEmailCode($request, $code),
            default => false,
        };

        if (! $valid) {
            return back()->withErrors([
                'code' => 'The verification code is invalid.',
            ])->onlyInput('code');
        }

        Auth::loginUsingId($user->id, (bool) $pending['remember']);
        $request->session()->forget('auth.two_factor');
        $request->session()->forget('auth.mfa_code_hash');
        $request->session()->regenerate();

        $this->completeLogin($request, $user);

        return redirect()->intended(route($this->dashboardRoute($user), absolute: false));
    }

    public function resendCode(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('auth.two_factor');
        if (! $pending) {
            return redirect()->route('login');
        }

        $user = User::find($pending['user_id']);
        if ($user) {
            $this->sendMfaCode($user);
        }

        return back()->with('status', 'A new verification code has been sent to your email.');
    }

    private function verifyTotpCode(User $user, string $code): bool
    {
        if (! $user->two_factor_secret) {
            return false;
        }

        $google2fa = new Google2FA;

        return $google2fa->verifyKey($user->two_factor_secret, $code, 1);
    }

    private function verifyBackupCode(User $user, string $code): bool
    {
        $backupCodes = json_decode($user->two_factor_backup_codes ?? '[]', true);

        $index = array_search($code, $backupCodes, true);
        if ($index !== false) {
            unset($backupCodes[$index]);
            $user->forceFill([
                'two_factor_backup_codes' => json_encode(array_values($backupCodes)),
            ])->save();

            return true;
        }

        return false;
    }

    private function verifyEmailCode(Request $request, string $code): bool
    {
        $stored = $request->session()->get('auth.mfa_code_hash');

        return $stored && Hash::check($code, $stored);
    }

    private function sendMfaCode(User $user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session()->put('auth.mfa_code_hash', Hash::make($code));

        try {
            Mail::to($user->email)->send(new MfaCode($code, $user->full_name ?? $user->name));
        } catch (\Exception $e) {
            Log::error('Failed to send MFA code email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function completeLogin(Request $request, ?User $user): void
    {
        $request->session()->put('last_activity_at', now());

        $user?->forceFill([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
        ])->save();

        event(new UserLoggedIn($user));
    }

    private function dashboardRoute(?User $user): string
    {
        return $user?->role === 'admin' ? 'admin.dashboard' : 'member.dashboard';
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
