<?php

use App\Http\Controllers\Admin\Setting\Profile\ProfileAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.admin"))->middleware(config("adminer.middleware.admin"))->group(function () {

    /**
     * Settings Profiles.
     */
    Route::apiResource("profiles", ProfileAdminController::class)->parameters([ "profiles" => "variable", ]);
    Route::post("profiles-import", [ ProfileAdminController::class, "import", ]);
    Route::get("profiles-export", [ ProfileAdminController::class, "export", ]);
});
