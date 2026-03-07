<?php

namespace App\Mail;

use App\Helpers\SettingHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string|null $appName
     * @param string|null $logoUrl
     * @param string|null $primaryColor
     * @param string|null $secondaryColor
     * @param string|null $tertiaryColor
     * @param string|null $eventStart
     * @param string|null $eventEnd
     * @param string|null $eventDescription
     * @param string|null $userName
     * @param string|null $userEmail
     * @param string|null $fromAddress
     * @param string|null $fromName
     * @return void
     */
    public function __construct(
        public ?string $appName = null,
        public ?string $logoUrl = null,
        public ?string $primaryColor = null,
        public ?string $secondaryColor = null,
        public ?string $tertiaryColor = null,
        public ?string $eventStart = null,
        public ?string $eventEnd = null,
        public ?string $eventDescription = null,
        public ?string $userName = null,
        public ?string $userEmail = null,
        public ?string $fromAddress = null,
        public ?string $fromName = null
    ) {
    }

    /**
     * @return array{primary: string, secondary: string, tertiary: string}
     */
    public function tenantColors(): array
    {
        $p = SettingHelper::get('COLOR_PRIMARY');
        $s = SettingHelper::get('COLOR_SECONDARY');
        $t = SettingHelper::get('COLOR_TERTIARY');

        return [
            'primary' => $this->primaryColor ?? ($p !== null ? trim((string) $p) : null),
            'secondary' => $this->secondaryColor ?? ($s !== null ? trim((string) $s) : null),
            'tertiary' => $this->tertiaryColor ?? ($t !== null ? trim((string) $t) : null),
        ];
    }

    /**
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        $subject = ($this->eventDescription !== null && trim($this->eventDescription) !== '')
            ? trim($this->eventDescription)
            : __("auth.email_event_and_account_info");
        $envelope = new Envelope(
            subject: $subject,
        );
        if ($this->fromAddress !== null || $this->fromName !== null) {
            $envelope = $envelope->from(new Address(
                $this->fromAddress ?? config("mail.from.address"),
                $this->fromName ?? config("mail.from.name")
            ));
        }
        return $envelope;
    }

    /**
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: "emails.verify-email",
        );
    }
}
