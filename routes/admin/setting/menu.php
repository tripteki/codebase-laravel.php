<?php

use App\Http\Controllers\Admin\Setting\Menu\MenuAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.admin"))->middleware(config("adminer.middleware.admin"))->group(function () {

    /**
     * Settings Menus.
     */
    Route::apiResource("bars.menus", MenuAdminController::class)->parameters([ "bars" => "bar", "menus" => "menu", ]);
    Route::post("menus-import", [ MenuAdminController::class, "import", ]);
    Route::get("menus-export", [ MenuAdminController::class, "export", ]);
});
