<?php

namespace Modules\Auth\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string|null $userName
     * @param string|null $userEmail
     * @param string $subjectKey
     * @param string $headingKey
     * @param string $lineKey
     * @return void
     */
    public function __construct(
        public ?string $userName = null,
        public ?string $userEmail = null,
        public string $subjectKey = "auth.account_activated_subject",
        public string $headingKey = "auth.account_activated_heading",
        public string $lineKey = "auth.account_activated_line",
    ) {
    }

    /**
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __($this->subjectKey),
        );
    }

    /**
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: "auth::emails.account-status",
        );
    }
}
