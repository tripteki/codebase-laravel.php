<?php

use App\Livewire\Admin\User\UserCreateComponent;
use App\Livewire\Admin\User\UserEditComponent;
use App\Livewire\Admin\User\UserIndexComponent;
use App\Livewire\Admin\User\UserShowComponent;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:web", "central.admin"])->prefix("/admin/users")->name("admin.users.")->group(function () {

    Route::get("/", UserIndexComponent::class)->name("index");
    Route::get("/create", UserCreateComponent::class)->name("create");
    Route::get("/{user}", UserShowComponent::class)->name("show");
    Route::get("/{user}/edit", UserEditComponent::class)->name("edit");
});
