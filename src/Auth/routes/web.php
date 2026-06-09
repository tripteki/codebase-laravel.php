<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\Auth\App\Http\Controllers\VerifyEmailController;

Route::get("auth/verify-email/{id}/{hash}", VerifyEmailController::class)
    ->middleware([
        "signed",
        ...Throttle::middleware("6,1"),
    ])
    ->name("verification.verify");
