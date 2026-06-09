<?php

use App\Http\Controllers\AppController;
use App\Support\Throttle;
use Illuminate\Support\Facades\Route;

Route::get("version", [ AppController::class, "version", ])
    ->middleware(Throttle::middleware("app-version"));

Route::get("status", [ AppController::class, "status", ])
    ->middleware(Throttle::middleware("app-status"));
