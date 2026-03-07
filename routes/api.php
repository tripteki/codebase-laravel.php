<?php

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

Route::get("version", [ AppController::class, "version", ])
    ->middleware("throttle:app-version");

Route::get("status", [ AppController::class, "status", ])
    ->middleware("throttle:app-status");
