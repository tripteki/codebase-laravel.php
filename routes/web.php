<?php

use Illuminate\Support\Facades\Route;

require __DIR__."/../src/V1/Api/Auth/Routes/web.php";

Route::get("/login", function () { return redirect("/admin/login"); })->name("login");
