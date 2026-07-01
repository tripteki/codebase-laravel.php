<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\Event\App\Http\Controllers\EventAdminController;

Route::middleware([...Throttle::middleware("api-read")])
    ->prefix("events")
    ->group(function (): void {
        Route::get("/stats/overview", [EventAdminController::class, "overview"]);
        Route::get("/", [EventAdminController::class, "index"]);
        Route::get("/{id}", [EventAdminController::class, "show"]);
    });

Route::middleware([...Throttle::middleware("api-write")])
    ->prefix("events")
    ->group(function (): void {
        Route::post("/", [EventAdminController::class, "store"]);
        Route::post("/import", [EventAdminController::class, "import"]);
        Route::post("/export", [EventAdminController::class, "export"]);
        Route::post("/{id}", [EventAdminController::class, "update"]);
        Route::match(["put", "patch"], "/{id}", [EventAdminController::class, "update"]);
        Route::delete("/{id}", [EventAdminController::class, "destroy"]);
    });
