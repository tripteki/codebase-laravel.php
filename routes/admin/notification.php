<?php

use App\Http\Controllers\Admin\NotificationController;
use App\Livewire\Admin\Notification\NotificationIndexComponent;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:web", "central.admin"])->prefix("/admin/notifications")->name("admin.notifications.")->group(function () {

    Route::get("/", function () {
        return redirect()->to(tenant_routes("admin.notifications.tab", ["tab" => "all"]));
    })->name("index");

    Route::get("/{tab}", NotificationIndexComponent::class)
        ->whereIn("tab", ["all", "unread", "read"])
        ->name("tab");
    Route::get("/data", [NotificationController::class, "getData"])->name("data");
    Route::patch("/mark-all-read", [NotificationController::class, "markAllAsRead"])->name("mark-all-read");
    Route::patch("/{id}/mark-read", [NotificationController::class, "markRead"])->name("mark-read");
    Route::get("/{id}/read-and-redirect", [NotificationController::class, "readAndRedirect"])->name("read-and-redirect");
});
