<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__."/../src/V1/Api/I18N/Routes/web.php";
require __DIR__."/../src/V1/Api/Auth/Routes/web.php";

Route::get("/login", function () { return redirect("/admin/login"); })->name("login");

Route::get("/manifest.json", function () {
    $manifest = [
        "name" => \Illuminate\Support\Str::headline(config("app.name")),
        "short_name" => \Illuminate\Support\Str::slug(config("app.name")),
        "description" => \Illuminate\Support\Str::headline(config("app.name")),
        "start_url" => "/",
        "display" => "standalone",
        "orientation" => "portrait-primary",
        "theme_color" => "#ffffff",
        "background_color" => "#ffffff",
        "scope" => "/",
        "icons" => [
            [
                "src" => asset("asset/favicon.png"),
                "sizes" => "128x128",
                "type" => "image/png",
                "purpose" => "any",
            ],
            [
                "src" => asset("asset/logo.png"),
                "sizes" => "512x512",
                "type" => "image/png",
                "purpose" => "any maskable",
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

Route::middleware([ "i18n", ])->group(function () {

    Route::get("/", function () { return Inertia::render("index"); });
});
