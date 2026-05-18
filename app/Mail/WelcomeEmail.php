<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $password = null
    ) {}

    public function build(): static
    {
        return $this->subject('Welcome to NSRC AMS')
            ->view('emails.welcome')
            ->with([
                'name' => $this->user->full_name,
                'email' => $this->user->email,
                'role' => ucfirst($this->user->role),
                'password' => $this->password,
                'loginUrl' => route('login'),
            ]);
    }
}
