<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\Log\App\Http\Controllers\ActivityAdminController;

Route::middleware([...Throttle::middleware("api-read")])
    ->prefix("activities")
    ->middleware("tenant.addon:modules_log")
    ->group(function (): void {
        Route::get("/", [ActivityAdminController::class, "index"]);
        Route::get("/{id}", [ActivityAdminController::class, "show"]);
    });
