<?php

use Src\V1\Api\Auth\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

/**
 * Auths.
 */
Route::get("auth/verify-email/{id}/{hash}", VerifyEmailController::class)
                ->middleware([ "signed", "throttle:6,1", ])
                ->name("verification.verify");
