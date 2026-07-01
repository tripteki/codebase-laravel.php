<?php

namespace Modules\User\App\Dtos;

use App\Models\Setting;
use Spatie\LaravelData\Data;

class SettingTransformerDto extends Data
{
    /**
     * @param string $id
     * @param string $key
     * @param string|null $value
     * @param string $value_kind
     * @param string|null $value_url
     * @return void
     */
    public function __construct(
        public string $id,
        public string $key,
        public ?string $value,
        public string $value_kind,
        public ?string $value_url = null,
    ) {}

    /**
     * @param Setting $setting
     * @return self
     */
    public static function fromSetting(Setting $setting): self
    {
        $value = $setting->value;
        $valueKind = self::inferValueKind($value);
        $valueUrl = null;

        if ($valueKind === "file" && filled($value)) {
            $valueUrl = asset("storage/".ltrim((string) $value, "/"));
        }

        return new self(
            id: (string) $setting->getKey(),
            key: (string) $setting->key,
            value: $value,
            value_kind: $valueKind,
            value_url: $valueUrl,
        );
    }

    /**
     * @param string|null $value
     * @return string
     */
    private static function inferValueKind(?string $value): string
    {
        return str_starts_with((string) $value, "setting-files/") ? "file" : "text";
    }
}
