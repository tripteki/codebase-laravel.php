<?php

namespace Modules\Auth\App\Mail;

use App\Helpers\AppNameHelper;
use App\Helpers\BrandingHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public string $appName;

    /**
     * @var string
     */
    public string $displayName;

    /**
     * @var string
     */
    public string $logoUrl;

    /**
     * @var string|null
     */
    public ?string $primaryColor;

    /**
     * @var string|null
     */
    public ?string $secondaryColor;

    /**
     * @var string|null
     */
    public ?string $tertiaryColor;

    /**
     * @param string $subjectLine
     * @param string $heading
     * @param string $line
     * @param string|null $userName
     * @param string|null $userEmail
     * @param string|null $line2
     * @param string|null $actionUrl
     * @param string|null $actionLabel
     * @param string|null $secondaryUrl
     * @param string|null $secondaryLabel
     * @return void
     */
    public function __construct(
        public string $subjectLine,
        public string $heading,
        public string $line,
        public ?string $userName = null,
        public ?string $userEmail = null,
        public ?string $line2 = null,
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
        public ?string $secondaryUrl = null,
        public ?string $secondaryLabel = null,
    ) {
        $branding = BrandingHelper::resolve();

        $this->appName = AppNameHelper::headline($branding["appName"]);
        $this->displayName = (string) $branding["displayName"];
        $this->logoUrl = $branding["logoUrl"];
        $this->primaryColor = $branding["primaryColor"];
        $this->secondaryColor = $branding["secondaryColor"];
        $this->tertiaryColor = $branding["tertiaryColor"];
    }

    /**
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: "auth::emails.notice",
        );
    }
}
