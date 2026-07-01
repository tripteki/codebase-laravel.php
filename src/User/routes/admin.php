<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\UserAdminController;

Route::middleware([...Throttle::middleware("api-read")])
    ->prefix("users")
    ->middleware("tenant.addon:modules_user")
    ->group(function (): void {
        Route::get("/", [UserAdminController::class, "index"]);
        Route::get("/stats/registrations", [UserAdminController::class, "registrationTrend"]);
        Route::get("/stats/roles", [UserAdminController::class, "usersByRole"]);
        Route::get("/{id}", [UserAdminController::class, "show"]);
    });

Route::middleware([...Throttle::middleware("api-write")])
    ->prefix("users")
    ->middleware("tenant.addon:modules_user")
    ->group(function (): void {
        Route::post("/", [UserAdminController::class, "store"]);
        Route::post("/import", [UserAdminController::class, "import"])->middleware("tenant.addon:features_import");
        Route::post("/export", [UserAdminController::class, "export"])->middleware("tenant.addon:features_export");
        Route::match(["put", "patch"], "/verify/{id}", [UserAdminController::class, "verify"]);
        Route::delete("/deactivate/{id}", [UserAdminController::class, "deactivate"]);
        Route::delete("/force-delete/{id}", [UserAdminController::class, "forceDelete"]);
        Route::delete("/activate/{id}", [UserAdminController::class, "activate"]);
        Route::match(["put", "patch"], "/{id}", [UserAdminController::class, "update"]);
    });
