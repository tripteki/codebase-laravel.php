<?php

use Src\V0\Log\Http\Controllers\Admin\Log\LogAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.admin"))->middleware(config("adminer.middleware.admin"))->group(function () {

    /**
     * Logs.
     */
    Route::apiResource("logs", LogAdminController::class)->only([ "index", "show", ])->parameters([ "logs" => "log", ]);
    Route::get("logs-export", [ LogAdminController::class, "export", ]);
});
