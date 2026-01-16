<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware("guest:web")->group(function () {

    Route::get("/admin/login", [ LoginController::class, "create", ])->name("admin.login");
    Route::post("/admin/login", [ LoginController::class, "store", ]);

    Route::get("/admin/register", [ RegisterController::class, "create", ])->name("admin.register");
    Route::post("/admin/register", [ RegisterController::class, "store", ]);

    Route::get("/admin/forgot-password", [ ForgotPasswordController::class, "create", ])->name("admin.password.request");
    Route::post("/admin/forgot-password", [ ForgotPasswordController::class, "store", ])->name("admin.password.email");

    Route::get("/admin/reset-password/{token}", [ ResetPasswordController::class, "create", ])->name("admin.password.reset");
    Route::post("/admin/reset-password", [ ResetPasswordController::class, "store", ])->name("admin.password.update");
});

Route::middleware("auth:web")->group(function () {

    Route::get("/admin", [ \App\Http\Controllers\Admin\DashboardController::class, "index", ])->name("admin.dashboard");
    Route::get("/admin/dashboard", [ \App\Http\Controllers\Admin\DashboardController::class, "index", ])->name("admin.dashboard.index");

    Route::post("/admin/logout", [ LoginController::class, "destroy", ])->name("admin.logout");
});
