<?php

namespace App\Helpers;

class AppNameHelper
{
    /**
     * @param string|null $value
     * @return string
     */
    public static function format(?string $value = null): string
    {
        $trimmed = trim((string) ($value ?? config("app.name")));

        if ($trimmed === "") {
            return "App";
        }

        $parts = preg_split("/\s+/", $trimmed) ?: [];

        return collect($parts)
            ->filter(static fn (string $part): bool => $part !== "")
            ->map(static fn (string $part): string => ucfirst(strtolower($part)))
            ->join(" ");
    }

    /**
     * @param string|null $value
     * @return string
     */
    public static function headline(?string $value = null): string
    {
        return strtoupper(self::format($value));
    }
}
