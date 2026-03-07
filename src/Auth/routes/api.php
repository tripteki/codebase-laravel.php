<?php

use Modules\Auth\App\Http\Controllers\RegisteredUserController;
use Modules\Auth\App\Http\Controllers\AuthenticatedController;
use Modules\Auth\App\Http\Controllers\EmailVerificationNotificationController;
use Modules\Auth\App\Http\Controllers\PasswordResetLinkController;
use Modules\Auth\App\Http\Controllers\NewPasswordController;
use Modules\Auth\App\Http\Controllers\VerifyEmailApiController;
use Modules\Auth\App\Http\Controllers\ResetPasswordApiController;
use Modules\User\App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post("v1/auth/register", [ RegisteredUserController::class, "store", ])
    ->middleware([ "guest:api", "throttle:api-register", ]);

Route::post("v1/auth/login", [ AuthenticatedController::class, "store", ])
    ->middleware([ "guest:api", "throttle:3,1", ]);

Route::post("v1/auth/logout", [ AuthenticatedController::class, "destroy", ])
    ->middleware([ "auth:api", "jwt.scope:ACCESS_TOKEN", "throttle:api-read", ]);

Route::match([ "put", "patch", ], "v1/auth/refresh", [ AuthenticatedController::class, "update", ])
    ->middleware([ "auth:api", "jwt.scope:REFRESH_TOKEN", "throttle:3,1", ]);

Route::get("v1/auth/me", [ UserController::class, "show", ])
    ->middleware([ "auth:api", "jwt.scope:ACCESS_TOKEN", "throttle:api-read", ]);

Route::post("v1/auth/email/verification-notification", [ EmailVerificationNotificationController::class, "store", ])
    ->middleware([ "auth:api", "jwt.scope:ACCESS_TOKEN", "throttle:3,1", ]);

Route::post("v1/auth/forgot-password", [ PasswordResetLinkController::class, "store", ])
    ->middleware([ "guest:api", "throttle:3,1", ]);

Route::post("v1/auth/reset-password", [ NewPasswordController::class, "store", ])
    ->middleware([ "guest:api", "throttle:api-read", ]);

Route::post("v1/auth/verify-email/{email}", [ VerifyEmailApiController::class, "store", ])
    ->middleware([ "guest:api", "throttle:api-read", ])
    ->where("email", ".+");

Route::post("v1/auth/reset-password/{email}", [ ResetPasswordApiController::class, "store", ])
    ->middleware([ "guest:api", "throttle:api-read", ])
    ->where("email", ".+");
