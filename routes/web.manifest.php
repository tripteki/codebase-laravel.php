<?php

use App\Helpers\SettingHelper;
use Illuminate\Support\Facades\Route;

Route::get("/manifest.json", function () {

    $tenantSegment = request()->route("tenant");
    $basePath = $tenantSegment ? "/" . $tenantSegment . "/" : "/";

    $baseName = \Illuminate\Support\Str::headline(config("app.name"));
    $tenantTitle = (config("tenancy.is_tenancy") && tenancy()->initialized)
        ? (tenant("title") ? \Illuminate\Support\Str::headline(tenant("title")) : null)
        : null;
    $appName = $tenantTitle ?: $baseName;
    $shortName = $tenantTitle && config("tenancy.is_tenancy") && tenancy()->initialized && tenant("slug")
        ? \Illuminate\Support\Str::slug(tenant("slug"))
        : \Illuminate\Support\Str::slug(config("app.name"));

    $defaultFaviconPng = asset("asset/favicon.png");
    $defaultLogoPng = asset("asset/logo.png");

    $tenantFaviconPng = (config("tenancy.is_tenancy") && hasTenant() && tenant("favicon_png")) ? asset("storage/" . tenant("favicon_png")) : $defaultFaviconPng;
    $tenantLogoPng = (config("tenancy.is_tenancy") && hasTenant() && tenant("icon")) ? asset("storage/" . tenant("icon")) : $defaultLogoPng;

    $defaultPrimaryHex = ($p = SettingHelper::get("COLOR_PRIMARY")) !== null ? trim((string) $p) : null;
    $defaultBackgroundHex = "#ffffff";

    $primaryHex = (config("tenancy.is_tenancy") && hasTenant() && tenant("primary_color")) ? (string) tenant("primary_color") : $defaultPrimaryHex;
    $backgroundHex = $defaultBackgroundHex;

    $manifest = [

        "name" => $appName,
        "short_name" => $shortName,
        "description" => $appName,
        "start_url" => $basePath,
        "display" => "standalone",
        "orientation" => "portrait-primary",
        "theme_color" => $primaryHex ?? "",
        "background_color" => $backgroundHex,
        "scope" => $basePath,
        "icons" => [
            [
                "src" => $tenantFaviconPng,
                "sizes" => "128x128",
                "type" => "image/png",
                "purpose" => "any",
            ],
            [
                "src" => $tenantLogoPng,
                "sizes" => "512x512",
                "type" => "image/png",
                "purpose" => "any",
            ],
            [
                "src" => $tenantLogoPng,
                "sizes" => "512x512",
                "type" => "image/png",
                "purpose" => "maskable",
            ],
        ],
        "categories" => ["productivity", "utilities"],
        "prefer_related_applications" => false,
    ];

    return response()
        ->json($manifest, 200)
        ->header("Content-Type", "application/manifest+json");

})->name("manifest");

Route::get("/offline", function () {

    return response()->view("offline", [], 200);

})->name("offline")->middleware("i18n");
