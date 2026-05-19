<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'full_name' => ['nullable', 'string', 'max:255'],
            'school_id' => ['nullable', 'string', 'max:255'],
            'personal_contact_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'college' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'string', 'max:50'],
            'terms' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'full_name' => $request->full_name ?: $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'member',
            'status' => 'pending',
            'school_id' => $request->school_id,
            'personal_contact_number' => $request->personal_contact_number,
            'gender' => $request->gender,
            'college' => $request->college,
            'major' => $request->major,
            'year_level' => $request->year_level,
        ]);

        event(new Registered($user));

        if ($user->email_notifications_enabled ?? true) {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        }

        return redirect()->route('login')->with('status', 'Registration successful! Please wait for administrator approval before logging in.');
    }
}
