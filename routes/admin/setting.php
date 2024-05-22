<?php

use App\Http\Controllers\Admin\Setting\SettingAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.admin"))->middleware(config("adminer.middleware.admin"))->group(function () {

    /**
     * Settings.
     */
    Route::apiResource("settings", SettingAdminController::class)->parameters([ "settings" => "key", ]);
    Route::post("settings-import", [ SettingAdminController::class, "import", ]);
    Route::get("settings-export", [ SettingAdminController::class, "export", ]);
});
