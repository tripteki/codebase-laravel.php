<?php

use App\Http\Controllers\Setting\Locale\LocaleController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.user"))->middleware(config("adminer.middleware.user"))->group(function () {

    /**
     * Settings Locales.
     */
    Route::get("locales", [ LocaleController::class, "index", ]);
    Route::put("locales", [ LocaleController::class, "update", ]);
});
