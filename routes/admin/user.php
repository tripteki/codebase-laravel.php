<?php

use App\Http\Controllers\Admin\User\UserAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.admin"))->middleware(config("adminer.middleware.admin"))->group(function () {

    /**
     * Users.
     */
    Route::apiResource("users", UserAdminController::class)->parameters([ "users" => "identifier", ]);
    Route::post("users-import", [ UserAdminController::class, "import", ]);
    Route::get("users-export", [ UserAdminController::class, "export", ]);
});
