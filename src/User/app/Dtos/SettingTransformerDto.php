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
     * @param \DateTimeInterface|null $created_at
     * @param \DateTimeInterface|null $updated_at
     * @return void
     */
    public function __construct(
        public string $id,
        public string $key,
        public ?string $value,
        public string $value_kind,
        public ?string $value_url = null,
        public ?\DateTimeInterface $created_at = null,
        public ?\DateTimeInterface $updated_at = null,
    ) {}

    /**
     * @param \App\Models\Setting $setting
     * @return self
     */
    public static function fromSetting(Setting $setting): self
    {
        $value = $setting->value;
        $storedKind = $setting->value_kind ?? null;
        $valueKind = $storedKind === "file" || $storedKind === "text"
            ? $storedKind
            : self::inferValueKind($value);
        $valueUrl = null;

        if ($valueKind === "file" && filled($value)) {
            $valueUrl = public_storage_url((string) $value);
        }

        return new self(
            id: (string) $setting->getKey(),
            key: (string) $setting->key,
            value: $value,
            value_kind: $valueKind,
            value_url: $valueUrl,
            created_at: $setting->created_at,
            updated_at: $setting->updated_at,
        );
    }

    /**
     * @param string|null $value
     * @return string
     */
    private static function inferValueKind(?string $value): string
    {
        return is_setting_file_path($value) ? "file" : "text";
    }
}
