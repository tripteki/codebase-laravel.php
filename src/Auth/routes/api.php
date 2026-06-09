<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\Auth\App\Http\Controllers\AuthenticatedController;
use Modules\Auth\App\Http\Controllers\EmailVerificationNotificationController;
use Modules\Auth\App\Http\Controllers\NewPasswordController;
use Modules\Auth\App\Http\Controllers\PasswordResetLinkController;
use Modules\Auth\App\Http\Controllers\RegisteredUserController;
use Modules\Auth\App\Http\Controllers\ResetPasswordApiController;
use Modules\Auth\App\Http\Controllers\VerifyEmailApiController;
use Modules\User\App\Http\Controllers\UserController;

Route::post("v1/auth/register", [ RegisteredUserController::class, "store", ])
    ->middleware([
        "guest:api",
        ...Throttle::middleware("api-register"),
    ]);

Route::post("v1/auth/login", [ AuthenticatedController::class, "store", ])
    ->middleware([
        "guest:api",
        ...Throttle::middleware("3,1"),
    ]);

Route::post("v1/auth/logout", [ AuthenticatedController::class, "destroy", ])
    ->middleware([
        "auth:api",
        "jwt.scope:ACCESS_TOKEN",
        ...Throttle::middleware("api-write"),
    ]);

Route::match([ "put", "patch", ], "v1/auth/refresh", [ AuthenticatedController::class, "update", ])
    ->middleware([
        "auth:api",
        "jwt.scope:REFRESH_TOKEN",
        ...Throttle::middleware("api-refresh"),
    ]);

Route::get("v1/auth/me", [ UserController::class, "show", ])
    ->middleware([
        "auth:api",
        "jwt.scope:ACCESS_TOKEN",
        ...Throttle::middleware("api-read"),
    ]);

Route::post("v1/auth/email/verification-notification", [ EmailVerificationNotificationController::class, "store", ])
    ->middleware([
        "auth:api",
        "jwt.scope:ACCESS_TOKEN",
        ...Throttle::middleware("3,1"),
    ]);

Route::post("v1/auth/forgot-password", [ PasswordResetLinkController::class, "store", ])
    ->middleware([
        "guest:api",
        ...Throttle::middleware("3,1"),
    ]);

Route::post("v1/auth/reset-password", [ NewPasswordController::class, "store", ])
    ->middleware([
        "guest:api",
        ...Throttle::middleware("api-write"),
    ]);

Route::post("v1/auth/verify-email/{email}", [ VerifyEmailApiController::class, "store", ])
    ->middleware(Throttle::middleware("api-write"))
    ->where("email", ".+");

Route::post("v1/auth/reset-password/{email}", [ ResetPasswordApiController::class, "store", ])
    ->middleware(Throttle::middleware("api-write"))
    ->where("email", ".+");
