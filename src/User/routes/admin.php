<?php

use Modules\User\App\Http\Controllers\UserAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware([ "auth:api", "jwt.scope:ACCESS_TOKEN", "verified", "throttle:api-read", ])->prefix("v1/admin/users")->group(function (): void {

    Route::get("/", [ UserAdminController::class, "index", ]);
    Route::post("/", [ UserAdminController::class, "store", ]);
    Route::post("/import", [ UserAdminController::class, "import", ]);
    Route::post("/export", [ UserAdminController::class, "export", ]);
    Route::match([ "put", "patch", ], "/verify/{id}", [ UserAdminController::class, "verify", ]);
    Route::delete("/deactivate/{id}", [ UserAdminController::class, "deactivate", ]);
    Route::delete("/activate/{id}", [ UserAdminController::class, "activate", ]);
    Route::get("/{id}", [ UserAdminController::class, "show", ]);
    Route::match([ "put", "patch", ], "/{id}", [ UserAdminController::class, "update", ]);
});
