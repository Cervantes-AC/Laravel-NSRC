<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\AlertService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        $this->ensureCaptchaIsValid();

        $user = User::where('email', $this->string('email'))->first();

        if ($user?->locked_until && $user->locked_until->isFuture()) {
            throw ValidationException::withMessages([
                'email' => 'This account is temporarily locked. Try again later.',
            ]);
        }

        if ($user && $user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => match ($user->status) {
                    'suspended' => 'This account is suspended. Please contact an administrator.',
                    'inactive' => 'This account is inactive. Please contact an administrator.',
                    'rejected' => 'This account is not approved for access.',
                    default => 'This account is pending administrator approval.',
                },
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            if ($user) {
                $attempts = (int) $user->failed_login_attempts + 1;
                $user->forceFill(['failed_login_attempts' => $attempts]);

                if ($attempts >= 5) {
                    $user->forceFill(['locked_until' => now()->addMinutes(15)]);
                }

                $user->save();

                if ($attempts >= 3) {
                    $this->generateCaptchaChallenge();
                }

                app(AlertService::class)->checkFailedLoginAttempts($user, $attempts);
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        $this->session()->forget(['auth.captcha.required', 'auth.captcha.answer']);
    }

    /**
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    private function ensureCaptchaIsValid(): void
    {
        if (! $this->session()->get('auth.captcha.required')) {
            return;
        }

        if ((string) $this->input('captcha_answer') === (string) $this->session()->get('auth.captcha.answer')) {
            return;
        }

        $this->generateCaptchaChallenge();

        throw ValidationException::withMessages([
            'captcha_answer' => 'Please solve the security check correctly.',
        ]);
    }

    private function generateCaptchaChallenge(): void
    {
        $left = random_int(1, 9);
        $right = random_int(1, 9);

        $this->session()->put('auth.captcha.required', true);
        $this->session()->put('auth.captcha.question', "{$left} + {$right}");
        $this->session()->put('auth.captcha.answer', $left + $right);
    }
}
