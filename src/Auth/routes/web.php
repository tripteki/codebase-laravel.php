<?php

use Modules\Auth\App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get("auth/verify-email/{id}/{hash}", VerifyEmailController::class)
                ->middleware([ "signed", "throttle:6,1", ])
                ->name("verification.verify");
