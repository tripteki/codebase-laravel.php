<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\SettingVariablesController;
use Modules\User\App\Http\Controllers\UserController;
use Modules\User\App\Http\Controllers\WebPushSubscriptionController;

Route::get("v1/settings/variables", [ SettingVariablesController::class, "index", ]);

Route::middleware([
    "auth:api",
    "jwt.scope:ACCESS_TOKEN",
    "verified",
    "tenant.user",
    ...Throttle::middleware("api-read"),
])->group(function (): void {
    Route::get("v1/users/me", [ UserController::class, "show", ]);
    Route::get("v1/users/me/accesses", [ UserController::class, "access", ]);
    Route::get("v1/users/me/interests", [ UserController::class, "interests", ]);
});

Route::middleware([
    "auth:api",
    "jwt.scope:ACCESS_TOKEN",
    "verified",
    "tenant.user",
    ...Throttle::middleware("api-write"),
])->group(function (): void {
    Route::match([ "put", "patch", ], "v1/users/me", [ UserController::class, "update", ]);
    Route::post("v1/users/me", [ UserController::class, "updateMultipart", ]);
    Route::post("v1/webpush/subscribe", [ WebPushSubscriptionController::class, "store", ]);
    Route::post("v1/webpush/unsubscribe", [ WebPushSubscriptionController::class, "destroy", ]);
});
