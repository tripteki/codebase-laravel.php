<?php

use App\Http\Controllers\Admin\NotificationController;
use App\Livewire\Admin\Notification\NotificationIndexComponent;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:web")->prefix("/admin/notifications")->name("admin.notifications.")->group(function () {

    Route::get("/", NotificationIndexComponent::class)->name("index");
    Route::get("/data", [NotificationController::class, "getData"])->name("data");
    Route::patch("/mark-all-read", [NotificationController::class, "markAllAsRead"])->name("mark-all-read");
    Route::patch("/{id}/mark-read", [NotificationController::class, "markRead"])->name("mark-read");
});
