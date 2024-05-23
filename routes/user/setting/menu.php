<?php

use App\Http\Controllers\Setting\Menu\MenuController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.user"))->middleware(config("adminer.middleware.user"))->group(function () {

    /**
     * Settings Menus.
     */
    Route::get("menus", [ MenuController::class, "index", ]);
    Route::post("menus", [ MenuController::class, "store", ]);
    Route::put("menus", [ MenuController::class, "update", ]);
    Route::delete("menus/{bar}/{menu}", [ MenuController::class, "destroy", ]);
});
