<?php

namespace Modules\Auth\App\Mail;

use App\Helpers\AppNameHelper;
use App\Helpers\BrandingHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $appName;

    public string $displayName;

    public string $logoUrl;

    public ?string $primaryColor;

    public ?string $secondaryColor;

    public ?string $tertiaryColor;

    /**
     * @param string|null $appName
     * @param string|null $logoUrl
     * @param string|null $primaryColor
     * @param string|null $secondaryColor
     * @param string|null $tertiaryColor
     * @param string|null $userName
     * @param string|null $userEmail
     * @param string|null $verificationUrl
     * @param ?string $userNameLabel
     * @return void
     */
    public function __construct(
        ?string $appName = null,
        ?string $logoUrl = null,
        ?string $primaryColor = null,
        ?string $secondaryColor = null,
        ?string $tertiaryColor = null,
        public ?string $userName = null,
        public ?string $userNameLabel = null,
        public ?string $userEmail = null,
        public ?string $verificationUrl = null,
    ) {
        $branding = BrandingHelper::resolve();

        $this->appName = AppNameHelper::headline($appName ?? $branding["appName"]);
        $this->displayName = (string) $branding["displayName"];
        $this->logoUrl = $logoUrl ?? $branding["logoUrl"];
        $this->primaryColor = $primaryColor ?? $branding["primaryColor"];
        $this->secondaryColor = $secondaryColor ?? $branding["secondaryColor"];
        $this->tertiaryColor = $tertiaryColor ?? $branding["tertiaryColor"];
    }

    /**
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __("auth.email_verification_subject"),
        );
    }

    /**
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: "auth::emails.verify-email",
        );
    }
}
