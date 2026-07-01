<?php

namespace Modules\Event\App\Dtos;

use Carbon\Carbon;
use Modules\Event\App\Models\Event;
use Modules\Event\App\Support\AddOnsHelper;
use Modules\Event\App\Support\EventBrandingSupport;
use Spatie\LaravelData\Data;

class EventTransformerDto extends Data
{
    /**
     * @param string $id
     * @param string $title
     * @param string|null $description
     * @param string|null $primary_color
     * @param string|null $secondary_color
     * @param string|null $tertiary_color
     * @param string|null $icon
     * @param string|null $icon_url
     * @param string|null $favicon_ico
     * @param string|null $favicon_ico_url
     * @param string|null $favicon_png
     * @param string|null $favicon_png_url
     * @param string|null $brand_light
     * @param string|null $brand_light_url
     * @param string|null $brand_dark
     * @param string|null $brand_dark_url
     * @param string|null $copyright_text
     * @param string|null $copyright_image
     * @param string|null $copyright_image_url
     * @param list<string> $add_ons_features
     * @param list<string> $add_ons_modules
     * @param array<string, array<string, string|null>> $add_ons_config
     * @param \DateTimeInterface|null $created_at
     * @param \DateTimeInterface|null $updated_at
     * @return void
     */
    public function __construct(
        public string $id,
        public string $title,
        public ?string $description,
        public ?string $primary_color,
        public ?string $secondary_color,
        public ?string $tertiary_color,
        public ?string $icon,
        public ?string $icon_url,
        public ?string $favicon_ico,
        public ?string $favicon_ico_url,
        public ?string $favicon_png,
        public ?string $favicon_png_url,
        public ?string $brand_light,
        public ?string $brand_light_url,
        public ?string $brand_dark,
        public ?string $brand_dark_url,
        public ?string $copyright_text,
        public ?string $copyright_image,
        public ?string $copyright_image_url,
        public array $add_ons_features,
        public array $add_ons_modules,
        public array $add_ons_config,
        public ?\DateTimeInterface $created_at,
        public ?\DateTimeInterface $updated_at,
    ) {}

    /**
     * @param Event $event
     * @return self
     */
    public static function fromEvent(Event $event): self
    {
        $brandingPath = static fn (string $field): ?string => self::nullableString($event->getAttribute($field));

        return new self(
            id: (string) $event->getKey(),
            title: (string) ($event->getAttribute("title") ?? $event->getKey()),
            description: self::nullableString($event->getAttribute("description")),
            primary_color: self::nullableString($event->getAttribute("primary_color")),
            secondary_color: self::nullableString($event->getAttribute("secondary_color")),
            tertiary_color: self::nullableString($event->getAttribute("tertiary_color")),
            icon: $brandingPath("icon"),
            icon_url: EventBrandingSupport::publicUrl($brandingPath("icon")),
            favicon_ico: $brandingPath("favicon_ico"),
            favicon_ico_url: EventBrandingSupport::publicUrl($brandingPath("favicon_ico")),
            favicon_png: $brandingPath("favicon_png"),
            favicon_png_url: EventBrandingSupport::publicUrl($brandingPath("favicon_png")),
            brand_light: $brandingPath("brand_light"),
            brand_light_url: EventBrandingSupport::publicUrl($brandingPath("brand_light")),
            brand_dark: $brandingPath("brand_dark"),
            brand_dark_url: EventBrandingSupport::publicUrl($brandingPath("brand_dark")),
            copyright_text: self::nullableString($event->getAttribute("copyright_text")),
            copyright_image: $brandingPath("copyright_image"),
            copyright_image_url: EventBrandingSupport::publicUrl($brandingPath("copyright_image")),
            add_ons_features: AddOnsHelper::featureValues($event),
            add_ons_modules: AddOnsHelper::moduleValues($event),
            add_ons_config: self::sanitizeConfigForResponse(AddOnsHelper::config($event)),
            created_at: self::castDate($event->created_at),
            updated_at: self::castDate($event->updated_at),
        );
    }

    /**
     * @param array<string, array<string, string|null>> $config
     * @return array<string, array<string, string|null>>
     */
    private static function sanitizeConfigForResponse(array $config): array
    {
        $sanitized = $config;

        foreach ($sanitized as $feature => $rows) {
            if (! is_array($rows)) {
                continue;
            }

            if (isset($rows["password"])) {
                $rows["password"] = null;
                $sanitized[$feature] = $rows;
            }
        }

        return $sanitized;
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    private static function nullableString(mixed $value): ?string
    {
        if ($value === null || $value === "") {
            return null;
        }

        return (string) $value;
    }

    /**
     * @param mixed $value
     * @return \DateTimeInterface|null
     */
    private static function castDate(mixed $value): ?\DateTimeInterface
    {
        if ($value === null || $value === "") {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        return Carbon::parse($value);
    }
}
