<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\UserAdminController;

Route::middleware([
    "auth:api",
    "jwt.scope:ACCESS_TOKEN",
    "verified",
    ...Throttle::middleware("api-read"),
])->prefix("v1/admin/users")->group(function (): void {
    Route::get("/", [ UserAdminController::class, "index", ]);
    Route::get("/{id}", [ UserAdminController::class, "show", ]);
});

Route::middleware([
    "auth:api",
    "jwt.scope:ACCESS_TOKEN",
    "verified",
    ...Throttle::middleware("api-write"),
])->prefix("v1/admin/users")->group(function (): void {
    Route::post("/", [ UserAdminController::class, "store", ]);
    Route::post("/import", [ UserAdminController::class, "import", ]);
    Route::post("/export", [ UserAdminController::class, "export", ]);
    Route::match([ "put", "patch", ], "/verify/{id}", [ UserAdminController::class, "verify", ]);
    Route::delete("/deactivate/{id}", [ UserAdminController::class, "deactivate", ]);
    Route::delete("/force-delete/{id}", [ UserAdminController::class, "forceDelete", ]);
    Route::delete("/activate/{id}", [ UserAdminController::class, "activate", ]);
    Route::match([ "put", "patch", ], "/{id}", [ UserAdminController::class, "update", ]);
});
