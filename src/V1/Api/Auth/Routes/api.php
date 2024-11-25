<?php

use Src\V1\Api\Auth\Http\Controllers\RegisteredUserController;
use Src\V1\Api\Auth\Http\Controllers\AuthenticatedController;
use Src\V1\Api\Auth\Http\Controllers\EmailVerificationNotificationController;
use Src\V1\Api\Auth\Http\Controllers\PasswordResetLinkController;
use Src\V1\Api\Auth\Http\Controllers\NewPasswordController;
use Src\V1\Api\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

/**
 * Auths.
 */
Route::post("v1/auth/register", [ RegisteredUserController::class, "store", ])
                ->middleware("guest:api");

Route::post("v1/auth/login", [ AuthenticatedController::class, "store", ])
                ->middleware("guest:api");

Route::post("v1/auth/logout", [ AuthenticatedController::class, "destroy", ])
                ->middleware("auth:api");

Route::match([ "put", "patch", ], "v1/auth/refresh", [ AuthenticatedController::class, "update", ])
                ->middleware("auth:api");

Route::get("v1/auth/me", [ UserController::class, "show", ])
                ->middleware("auth:api");

Route::post("v1/auth/email/verification-notification", [ EmailVerificationNotificationController::class, "store", ])
                ->middleware([ "auth:api", "throttle:6,1", ]);

Route::post("v1/auth/forgot-password", [ PasswordResetLinkController::class, "store", ])
                ->middleware("guest:api");

Route::post("v1/auth/reset-password", [ NewPasswordController::class, "store", ])
                ->middleware("guest:api");
