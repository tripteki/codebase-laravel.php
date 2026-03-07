<?php

namespace Modules\Auth\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string|null $userName
     * @param string|null $userEmail
     * @param string|null $resetLink
     * @return void
     */
    public function __construct(
        public ?string $userName = null,
        public ?string $userEmail = null,
        public ?string $resetLink = null,
    ) {
    }

    /**
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __("auth.password_reset_subject"),
        );
    }

    /**
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: "auth::emails.reset-password",
        );
    }
}
