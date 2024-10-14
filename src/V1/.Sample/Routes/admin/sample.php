<?php

use App\Enums\Role;
use App\Enums\Permission;
use Src\V1\Sample\Http\Controllers\API\Admin\SampleAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix("admin/master")->middleware([ "auth:api", "verified", "role:".(Role::ADMINISTRATOR)->value, ])->group(function () {

    /**
     * Samples.
     */
    Route::post("samples/import", [ SampleAdminController::class, "import", ]);
    Route::get("samples/export", [ SampleAdminController::class, "export", ]);
    Route::get("samples/select", [ SampleAdminController::class, "select", ]);
    Route::put("samples/restore/{id}", [ SampleAdminController::class, "restore", ]);
    Route::apiResource("samples", SampleAdminController::class)->parameters([ "samples" => "id", ]);
});
