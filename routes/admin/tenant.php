<?php

use App\Livewire\Admin\Event\EventCreateComponent;
use App\Livewire\Admin\Event\EventEditComponent;
use App\Livewire\Admin\Event\EventIndexComponent;
use App\Livewire\Admin\Event\EventShowComponent;
use App\Livewire\Admin\Tenant\ContentIndexComponent;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:web", "central.admin"])->prefix("/admin/tenants")->name("admin.tenants.")->group(function () {

    Route::get("/content/{tenant}", ContentIndexComponent::class)->name("content.index");
    Route::get("/events", EventIndexComponent::class)->name("events.index");
    Route::get("/events/create", EventCreateComponent::class)->name("events.create");
    Route::get("/events/{tenant}", EventShowComponent::class)->name("events.show");
    Route::get("/events/{tenant}/edit", EventEditComponent::class)->name("events.edit");
});
