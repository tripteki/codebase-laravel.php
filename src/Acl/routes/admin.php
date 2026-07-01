<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\Acl\App\Http\Controllers\PermissionAdminController;
use Modules\Acl\App\Http\Controllers\RoleAdminController;

Route::middleware([...Throttle::middleware("api-read")])
    ->prefix("roles")
    ->middleware("tenant.addon:modules_acl")
    ->group(function (): void {
        Route::get("/", [RoleAdminController::class, "index"]);
        Route::get("/{id}", [RoleAdminController::class, "show"]);
    });

Route::middleware([...Throttle::middleware("api-write")])
    ->prefix("roles")
    ->middleware("tenant.addon:modules_acl")
    ->group(function (): void {
        Route::post("/", [RoleAdminController::class, "store"]);
        Route::match(["put", "patch"], "/{id}", [RoleAdminController::class, "update"]);
        Route::delete("/{id}", [RoleAdminController::class, "destroy"]);
        Route::post("/import", [RoleAdminController::class, "import"])->middleware("tenant.addon:features_import");
        Route::post("/export", [RoleAdminController::class, "export"])->middleware("tenant.addon:features_export");
    });

Route::middleware([...Throttle::middleware("api-read")])
    ->prefix("permissions")
    ->middleware("tenant.addon:modules_acl")
    ->group(function (): void {
        Route::get("/", [PermissionAdminController::class, "index"]);
        Route::get("/{id}", [PermissionAdminController::class, "show"]);
    });

Route::middleware([...Throttle::middleware("api-write")])
    ->prefix("permissions")
    ->middleware("tenant.addon:modules_acl")
    ->group(function (): void {
        Route::post("/", [PermissionAdminController::class, "store"]);
        Route::match(["put", "patch"], "/{id}", [PermissionAdminController::class, "update"]);
        Route::delete("/{id}", [PermissionAdminController::class, "destroy"]);
        Route::post("/import", [PermissionAdminController::class, "import"])->middleware("tenant.addon:features_import");
        Route::post("/export", [PermissionAdminController::class, "export"])->middleware("tenant.addon:features_export");
    });
