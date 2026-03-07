<?php

use App\Livewire\Admin\Activity\ActivityIndexComponent;
use App\Livewire\Admin\Activity\ActivityShowComponent;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:web", "central.admin"])->prefix("/admin/activities")->name("admin.activities.")->group(function () {

    Route::get("/", ActivityIndexComponent::class)->middleware("can:activity.view")->name("index");
    Route::get("/{activity}", ActivityShowComponent::class)->middleware("can:activity.view")->name("show");
});
