<?php

namespace Modules\Event\App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Event\App\Models\Event;

final class EventBrandingSupport
{
    /**
     * @var array<string, string>
     */
    public const DEFAULT_COLORS = [
        "primary_color" => "#2563eb",
        "secondary_color" => "#84cc16",
        "tertiary_color" => "#1e3a8a",
    ];

    /**
     * @var list<string>
     */
    public const FILE_FIELDS = [
        "icon",
        "favicon_ico",
        "favicon_png",
        "brand_light",
        "brand_dark",
        "copyright_image",
    ];

    /**
     * @param Event $event
     * @param array<string, UploadedFile|null> $files
     * @return void
     */
    public static function applyFiles(Event $event, array $files): void
    {
        $tenantId = (string) $event->getKey();

        foreach (self::FILE_FIELDS as $field) {
            $file = $files[$field] ?? null;

            if (! $file instanceof UploadedFile) {
                continue;
            }

            $previous = $event->getAttribute($field);

            if (filled($previous)) {
                Storage::disk("public")->delete((string) $previous);
            }

            $path = $file->store("event-branding/".$tenantId, "public");
            $event->setAttribute($field, $path);
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    public static function deleteFiles(Event $event): void
    {
        foreach (self::FILE_FIELDS as $field) {
            $path = $event->getAttribute($field);

            if (filled($path)) {
                Storage::disk("public")->delete((string) $path);
            }
        }
    }

    /**
     * @param Event $event
     * @param string $mode
     * @return void
     */
    public static function applyCopyrightMode(Event $event, string $mode): void
    {
        $mode = in_array($mode, ["none", "text", "image"], true) ? $mode : "none";

        if ($mode === "none") {
            $event->setAttribute("copyright_text", null);
            self::deleteFileField($event, "copyright_image");

            return;
        }

        if ($mode === "text") {
            self::deleteFileField($event, "copyright_image");

            return;
        }

        $event->setAttribute("copyright_text", null);
    }

    /**
     * @param Event $event
     * @param string $field
     * @return void
     */
    private static function deleteFileField(Event $event, string $field): void
    {
        $path = $event->getAttribute($field);

        if (! filled($path)) {
            return;
        }

        Storage::disk("public")->delete((string) $path);
        $event->setAttribute($field, null);
    }

    /**
     * @param string|null $path
     * @return string|null
     */
    public static function publicUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === "") {
            return null;
        }

        return asset("storage/".ltrim($path, "/"));
    }

    /**
     * @param Event $event
     * @return array{
     *     title: string,
     *     icon_url: string|null,
     *     favicon_ico_url: string|null,
     *     favicon_png_url: string|null,
     *     brand_light_url: string|null,
     *     brand_dark_url: string|null,
     *     copyright_text: string|null,
     *     copyright_image_url: string|null
     * }
     */
    public static function variables(Event $event): array
    {
        $assetUrl = static function (?string $path): ?string {
            if ($path === null || trim($path) === "") {
                return null;
            }

            return self::publicUrl($path);
        };

        $copyrightText = trim((string) ($event->getAttribute("copyright_text") ?? ""));

        return [
            "title" => (string) ($event->getAttribute("title") ?? ""),
            "icon_url" => $assetUrl($event->getAttribute("icon")),
            "favicon_ico_url" => $assetUrl($event->getAttribute("favicon_ico")),
            "favicon_png_url" => $assetUrl($event->getAttribute("favicon_png")),
            "brand_light_url" => $assetUrl($event->getAttribute("brand_light")),
            "brand_dark_url" => $assetUrl($event->getAttribute("brand_dark")),
            "copyright_text" => $copyrightText !== "" ? $copyrightText : null,
            "copyright_image_url" => $assetUrl($event->getAttribute("copyright_image")),
        ];
    }
}
