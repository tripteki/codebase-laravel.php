<?php

use Modules\User\App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware([ "auth:api", "jwt.scope:ACCESS_TOKEN", "verified", ])->group(function (): void {

    Route::get("v1/users/me", [ UserController::class, "show", ]);
});
