<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImportNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $filename,
        public int $total,
        public int $success,
        public int $failed,
        public int $skipped,
        public bool $overallSuccess
    ) {}

    public function build(): static
    {
        return $this->subject('Import Complete: ' . ($this->overallSuccess ? 'Success' : 'Completed with Errors'))
            ->view('emails.import-notification')
            ->with([
                'filename' => $this->filename,
                'total' => $this->total,
                'success' => $this->success,
                'failed' => $this->failed,
                'skipped' => $this->skipped,
                'overallSuccess' => $this->overallSuccess,
            ]);
    }
}
