<?php

use App\Http\Controllers\Setting\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.user"))->middleware(config("adminer.middleware.user"))->group(function () {

    /**
     * Settings Profiles.
     */
    Route::apiResource("profiles", ProfileController::class)->only([ "index", "update", ])->parameters([ "profiles" => "variable", ]);
});
