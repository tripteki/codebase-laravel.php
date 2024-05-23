<?php

use Src\V1\Post\Http\Controllers\API\PostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

require __DIR__."/admin/post.php";

Route::middleware("auth:api")->group(function () {

    /**
     * Posts.
     */
    Route::apiResource("posts", PostController::class)->parameters([ "posts" => "id", ]);
});
