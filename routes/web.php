<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__."/web.manifest.php";
require __DIR__."/../src/V1/Api/I18N/Routes/web.php";
require __DIR__."/../src/V1/Api/Auth/Routes/web.php";

Route::get("/login", function () { return redirect("/admin/login"); })->name("login");

Route::middleware([ "i18n", ])->group(function () {

    Route::get("/", function () { return Inertia::render("index"); });
});
