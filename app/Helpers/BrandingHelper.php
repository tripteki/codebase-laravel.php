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
        $appName = AppNameHelper::headline();

        return [
            "appName" => $appName,
            "displayName" => AppNameHelper::format(),
            "logoUrl" => frontend_url("manifest/asset/logo.png"),
            "primaryColor" => self::DEFAULT_COLORS["primaryColor"],
            "secondaryColor" => self::DEFAULT_COLORS["secondaryColor"],
            "tertiaryColor" => self::DEFAULT_COLORS["tertiaryColor"],
        ];
    }
}
