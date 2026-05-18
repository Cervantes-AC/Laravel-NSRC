<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLoggedIn;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

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
