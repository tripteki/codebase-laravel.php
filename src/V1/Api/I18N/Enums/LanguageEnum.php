<?php

namespace Src\V1\Api\I18N\Enums;

/**
 * @enum LanguageEnum
 */
enum LanguageEnum: string
{
    case ENGLISH = "en";
    case INDONESIA = "id";

    /**
     * Get the label for the language.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {

            self::ENGLISH => "English",
            self::INDONESIA => "Indonesia",
        };
    }

    /**
     * Get all language labels as array.
     *
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
     * Get label by language code.
     *
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
