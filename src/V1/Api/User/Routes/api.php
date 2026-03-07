<?php

use Src\V1\Api\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

Route::middleware([ "auth:api", "verified", ])->group(function (): void {

    Route::get("v1/users/me", [ UserController::class, "show", ]);
});
