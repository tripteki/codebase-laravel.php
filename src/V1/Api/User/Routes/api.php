<?php

use Src\V1\Api\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

Route::middleware([ "auth:api", "verified", ])->group(function () {

    /**
     * Users.
     */
    Route::get("v1/users", [ UserController::class, "index", ]);
    Route::get("v1/users/me", [ UserController::class, "show", ]);
    Route::match([ "put", "patch", ], "v1/users", [ UserController::class, "update", ]);
    Route::post("v1/users", [ UserController::class, "store", ]);
    Route::delete("v1/users", [ UserController::class, "destroy", ]);
});
