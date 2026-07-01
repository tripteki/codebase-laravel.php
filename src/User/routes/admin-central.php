<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\SettingAdminController;

Route::middleware([
    "auth:api",
    "jwt.scope:ACCESS_TOKEN",
    "verified",
    "central.admin",
    ...Throttle::middleware("api-read"),
])->prefix("v1/admin/settings")->group(function (): void {
    Route::get("/", [SettingAdminController::class, "index"]);
});

Route::middleware([
    "auth:api",
    "jwt.scope:ACCESS_TOKEN",
    "verified",
    "central.admin",
    ...Throttle::middleware("api-write"),
])->prefix("v1/admin/settings")->group(function (): void {
    Route::put("/", [SettingAdminController::class, "update"]);
});
