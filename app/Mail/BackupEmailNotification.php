<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BackupEmailNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $type,
        public bool $success,
        public string $filename,
        public string $size,
        public string $details,
        public array $summary
    ) {}

    public function build(): static
    {
        return $this->subject('Backup ' . ($this->success ? 'Completed' : 'Failed') . ': ' . ucfirst($this->type))
            ->view('emails.backup-notification')
            ->with([
                'type' => $this->type,
                'success' => $this->success,
                'filename' => $this->filename,
                'size' => $this->size,
                'details' => $this->details,
                'summary' => $this->summary,
            ]);
    }
}
