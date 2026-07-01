<?php

namespace App\Helpers;

class BrandingHelper
{
    /**
     * @var array<string, string>
     */
    private const DEFAULT_COLORS = [
        "primaryColor" => "#2563eb",
        "secondaryColor" => "#84cc16",
        "tertiaryColor" => "#1e3a8a",
    ];

    /**
     * @return array{
     *     appName: string,
     *     displayName: string,
     *     logoUrl: string,
     *     primaryColor: string,
     *     secondaryColor: string,
     *     tertiaryColor: string
     * }
     */
    public static function resolve(): array
    {
        return self::finalize([
            "appName" => AppNameHelper::headline(),
            "logoUrl" => self::resolveLogoUrl(),
            "primaryColor" => self::trimColor(SettingHelper::get("COLOR_PRIMARY")),
            "secondaryColor" => self::trimColor(SettingHelper::get("COLOR_SECONDARY")),
            "tertiaryColor" => self::trimColor(SettingHelper::get("COLOR_TERTIARY")),
        ]);
    }

    /**
     * @param array{
     *     appName: string,
     *     logoUrl: string,
     *     primaryColor: string|null,
     *     secondaryColor: string|null,
     *     tertiaryColor: string|null
     * } $branding
     * @return array{
     *     appName: string,
     *     displayName: string,
     *     logoUrl: string,
     *     primaryColor: string,
     *     secondaryColor: string,
     *     tertiaryColor: string
     * }
     */
    private static function finalize(array $branding): array
    {
        return [
            "appName" => $branding["appName"],
            "displayName" => self::resolveDisplayName($branding["appName"]),
            "logoUrl" => $branding["logoUrl"],
            "primaryColor" => $branding["primaryColor"] ?? self::DEFAULT_COLORS["primaryColor"],
            "secondaryColor" => $branding["secondaryColor"] ?? self::DEFAULT_COLORS["secondaryColor"],
            "tertiaryColor" => $branding["tertiaryColor"] ?? self::DEFAULT_COLORS["tertiaryColor"],
        ];
    }

    /**
     * @param string $appName
     * @return string
     */
    private static function resolveDisplayName(string $appName): string
    {
        return $appName;
    }

    /**
     * @return string
     */
    private static function resolveLogoUrl(): string
    {
        $value = trim((string) SettingHelper::get("LOGO", ""));

        if ($value !== "" && str_starts_with($value, "setting-files/")) {
            return asset("storage/".ltrim($value, "/"));
        }

        return frontend_url("manifest/asset/logo.png");
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    private static function trimColor(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed !== "" ? $trimmed : null;
    }
}
