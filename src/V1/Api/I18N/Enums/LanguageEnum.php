<?php

namespace Src\V1\Api\I18N\Enums;

/**
 * @enum LanguageEnum
 */
enum LanguageEnum: string
{
    /**
     * @var string
     */
    case ENGLISH = "en";

    /**
     * @var string
     */
    case INDONESIA = "id";

    /**
     * @var string
     */
    case MALAYSIA = "ms";

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {

            self::ENGLISH => "English",
            self::INDONESIA => "Indonesia",
            self::MALAYSIA => "Malaysia",
        };
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }

    /**
     * @param string $code
     * @return string|null
     */
    public static function getLabel(string $code): ?string
    {
        try {
            return self::from($code)->label();
        } catch (\ValueError $e) {
            return null;
        }
    }
}
