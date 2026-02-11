<?php

use App\Livewire\Admin\Permission\PermissionCreateComponent;
use App\Livewire\Admin\Permission\PermissionEditComponent;
use App\Livewire\Admin\Permission\PermissionIndexComponent;
use App\Livewire\Admin\Permission\PermissionShowComponent;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:web")->prefix("/admin/permissions")->name("admin.permissions.")->group(function () {

    Route::get("/", PermissionIndexComponent::class)->middleware("can:permission.view")->name("index");
    Route::get("/create", PermissionCreateComponent::class)->middleware("can:permission.create")->name("create");
    Route::get("/{permission}", PermissionShowComponent::class)->middleware("can:permission.view")->name("show");
    Route::get("/{permission}/edit", PermissionEditComponent::class)->middleware("can:permission.update")->name("edit");
});
