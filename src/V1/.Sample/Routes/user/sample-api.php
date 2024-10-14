<?php

use Src\V1\Sample\Http\Controllers\API\SampleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

Route::middleware([ "auth:api", "verified", ])->group(function () {

    /**
     * Samples.
     */
    Route::apiResource("samples", SampleController::class)->parameters([ "samples" => "id", ]);
});
