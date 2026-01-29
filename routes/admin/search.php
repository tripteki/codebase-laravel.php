<?php

use App\Http\Controllers\Admin\SearchController;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:web")->prefix("/admin/search")->name("admin.search.")->group(function () {

    Route::get("/", [SearchController::class, "index"])->name("index");
});
