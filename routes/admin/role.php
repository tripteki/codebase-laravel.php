<?php

use App\Livewire\Admin\Role\RoleCreateComponent;
use App\Livewire\Admin\Role\RoleEditComponent;
use App\Livewire\Admin\Role\RoleIndexComponent;
use App\Livewire\Admin\Role\RoleShowComponent;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:web", "central.admin"])->prefix("/admin/roles")->name("admin.roles.")->group(function () {

    Route::get("/", RoleIndexComponent::class)->middleware("can:role.view")->name("index");
    Route::get("/create", RoleCreateComponent::class)->middleware("can:role.create")->name("create");
    Route::get("/{role}", RoleShowComponent::class)->middleware("can:role.view")->name("show");
    Route::get("/{role}/edit", RoleEditComponent::class)->middleware("can:role.update")->name("edit");
});
