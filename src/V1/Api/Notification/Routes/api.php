<?php

use Src\V1\Api\Notification\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

Route::middleware([ "auth:api", "verified", ])->group(function (): void {

    Route::get("v1/notifications", [ NotificationController::class, "index", ]);
    Route::match([ "put", "patch", ], "v1/notifications/read-all", [ NotificationController::class, "readall", ]);
    Route::match([ "put", "patch", ], "v1/notifications/read/{id}", [ NotificationController::class, "read", ]);
    Route::get("v1/notifications/unread", [ NotificationController::class, "unread", ]);
    Route::get("v1/notifications/{id}", [ NotificationController::class, "show", ]);
    Route::delete("v1/notifications/{id}", [ NotificationController::class, "destroy", ]);
});
