<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAnnouncement extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Announcement $announcement
    ) {}

    public function build(): static
    {
        return $this->subject('New Announcement: ' . $this->announcement->title)
            ->view('emails.new-announcement')
            ->with([
                'name' => $this->user->full_name,
                'title' => $this->announcement->title,
                'content' => $this->announcement->body,
                'priority' => $this->announcement->priority ?? 'normal',
                'createdAt' => $this->announcement->created_at->format('F j, Y g:i A'),
            ]);
    }
}
