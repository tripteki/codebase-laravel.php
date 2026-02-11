<?php

use App\Livewire\Admin\User\UserCreateComponent;
use App\Livewire\Admin\User\UserEditComponent;
use App\Livewire\Admin\User\UserIndexComponent;
use App\Livewire\Admin\User\UserShowComponent;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:web")->prefix("/admin/users")->name("admin.users.")->group(function () {

    Route::get("/", UserIndexComponent::class)->middleware("can:user.view")->name("index");
    Route::get("/create", UserCreateComponent::class)->middleware("can:user.create")->name("create");
    Route::get("/{user}", UserShowComponent::class)->middleware("can:user.view")->name("show");
    Route::get("/{user}/edit", UserEditComponent::class)->middleware("can:user.update")->name("edit");
});
