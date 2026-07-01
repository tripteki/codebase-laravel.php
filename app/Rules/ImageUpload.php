<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ImageUpload implements ValidationRule
{
    /**
     * @var int
     */
    public const MAX_BYTES = 2 * 1024 * 1024;

    /**
     * @var array<int, string>
     */
    protected const EXTENSIONS = [ "jpg", "jpeg", "png", "gif", "webp", ];

    /**
     * @var array<int, string>
     */
    protected const MIME_TYPES = [ "image/jpeg", "image/png", "image/gif", "image/webp", ];

    /**
     * @param bool $allowIcon
     * @param bool $checkName
     * @return void
     */
    public function __construct(
        protected bool $allowIcon = false,
        protected bool $checkName = true,
    ) {}

    /**
     * @param string $attribute
     * @param mixed $value
     * @param \Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $typeKey = $this->allowIcon ? "upload.icon_type" : "upload.image_type";

        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail(__($typeKey));

            return;
        }

        if ($this->checkName) {
            $contentType = strtolower((string) $value->getClientMimeType());

            if (! in_array(strtolower($value->getClientOriginalExtension()), self::EXTENSIONS, true)
                || ($contentType !== "" && ! in_array($contentType, self::MIME_TYPES, true))) {
                $fail(__($typeKey));

                return;
            }
        }

        if ($value->getSize() > self::MAX_BYTES) {
            $fail(__("upload.too_large", [ "size" => self::MAX_BYTES / (1024 * 1024), ]));

            return;
        }

        if (self::detectExtension((string) file_get_contents($value->getRealPath()), $this->allowIcon) === null) {
            $fail(__($typeKey));
        }
    }

    /**
     * @param string $content
     * @param bool $allowIcon
     * @return string|null
     */
    public static function detectExtension(string $content, bool $allowIcon = false): ?string
    {
        return match (true) {
            str_starts_with($content, "\xFF\xD8\xFF") => "jpg",
            str_starts_with($content, "\x89PNG\r\n\x1A\n") => "png",
            in_array(substr($content, 0, 6), [ "GIF87a", "GIF89a", ], true) => "gif",
            substr($content, 0, 4) === "RIFF" && substr($content, 8, 4) === "WEBP" => "webp",
            $allowIcon && str_starts_with($content, "\x00\x00\x01\x00") => "ico",
            default => null,
        };
    }
}
