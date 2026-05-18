<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLoggedIn;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private readonly SettingsService $settings,
    ) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($this->settings->get('two_factor_enabled', false)) {
            $code = (string) random_int(100000, 999999);

            Auth::guard('web')->logout();
            $request->session()->regenerate();
            $request->session()->put('auth.two_factor', [
                'user_id' => $user?->id,
                'remember' => $request->boolean('remember'),
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(10)->timestamp,
            ]);

            try {
                Mail::raw("Your NSRC AMS verification code is {$code}. It expires in 10 minutes.", function ($message) use ($user): void {
                    $message->to($user->email, $user->name)->subject('NSRC AMS verification code');
                });
            } catch (\Throwable $exception) {
                $request->session()->forget('auth.two_factor');
                Log::error('Unable to send two-factor login code.', [
                    'user_id' => $user?->id,
                    'error' => $exception->getMessage(),
                ]);

                return redirect()->route('login')->withErrors([
                    'email' => 'The verification email could not be sent. Please check the mail settings or disable two-factor authentication temporarily.',
                ]);
            }

            return redirect()->route('two-factor.challenge')
                ->with('status', 'A verification code was sent to your email.');
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
            'code' => ['required', 'digits:6'],
        ]);

        $pending = $request->session()->get('auth.two_factor');

        if (! $pending || now()->timestamp > (int) $pending['expires_at']) {
            $request->session()->forget('auth.two_factor');

            return redirect()->route('login')->withErrors([
                'email' => 'The verification code expired. Please sign in again.',
            ]);
        }

        if (! Hash::check($request->string('code'), $pending['code_hash'])) {
            return back()->withErrors([
                'code' => 'The verification code is invalid.',
            ])->onlyInput('code');
        }

        Auth::loginUsingId($pending['user_id'], (bool) $pending['remember']);
        $request->session()->forget('auth.two_factor');
        $request->session()->regenerate();

        $user = Auth::user();
        $this->completeLogin($request, $user);

        return redirect()->intended(route($this->dashboardRoute($user), absolute: false));
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
