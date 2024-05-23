<?php

use App\Http\Controllers\Admin\Setting\Locale\LanguageAdminController;
use App\Http\Controllers\Admin\Setting\Locale\TranslationAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.admin"))->middleware(config("adminer.middleware.admin"))->group(function () {

    /**
     * Settings Locales.
     */
    Route::prefix("locales")->group(function () {

        Route::apiResource("languages", LanguageAdminController::class)->parameters([ "languages" => "code", ]);
        Route::post("languages-import", [ LanguageAdminController::class, "import", ]);
        Route::get("languages-export", [ LanguageAdminController::class, "export", ]);

        Route::apiResource("languages.translations", TranslationAdminController::class)->parameters([ "languages" => "code", "translations" => "key", ]);
        Route::post("languages/{code}/translations-import", [ TranslationAdminController::class, "import", ]);
        Route::get("languages/{code}/translations-export", [ TranslationAdminController::class, "export", ]);
    });
});
