<?php

use Src\V1\Post\Http\Controllers\API\Admin\PostAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix("admin/master")->middleware("auth:api")->group(function () {

    /**
     * Posts.
     */
    Route::post("posts/import", [ PostAdminController::class, "import", ]);
    Route::get("posts/export", [ PostAdminController::class, "export", ]);
    Route::get("posts/select", [ PostAdminController::class, "select", ]);
    Route::put("posts/restore/{id}", [ PostAdminController::class, "restore", ]);
    Route::apiResource("posts", PostAdminController::class)->parameters([ "posts" => "id", ]);
});
