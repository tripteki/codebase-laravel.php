<?php

use App\Livewire\Admin\Setting\UserSettingIndexComponent;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:web")->prefix("/admin/settings")->name("admin.settings.")->group(function () {

    Route::get("/{tab}", UserSettingIndexComponent::class)->name("tab")->where("tab", "personal|system");
    Route::get("/", function () { return redirect()->route("admin.settings.tab", ["tab" => "personal"]); })->name("index");
});
