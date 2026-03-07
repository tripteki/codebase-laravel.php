<?php

use App\Http\Controllers\Admin\AttachmentController;
use App\Livewire\Admin\Stage\MeetingCreateComponent;
use App\Livewire\Admin\Stage\MeetingEditComponent;
use App\Livewire\Admin\Stage\MeetingIndexComponent;
use App\Livewire\Admin\Stage\MeetingShowComponent;
use App\Livewire\Admin\Stage\SessionCreateComponent;
use App\Livewire\Admin\Stage\SessionEditComponent;
use App\Livewire\Admin\Stage\SessionIndexComponent;
use App\Livewire\Admin\Stage\SessionShowComponent;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:web"])->prefix("/admin/stage-meetings")->name("admin.stage.meetings.")->group(function () {
    Route::get("/", function () {
        return redirect(tenant_routes("admin.stage.meetings.table"));
    })->name("index");
    Route::get("/table", MeetingIndexComponent::class)->name("table");
    Route::get("/board", MeetingIndexComponent::class)->name("board");
    Route::get("/create", MeetingCreateComponent::class)->name("create");
    Route::get("/attachments/{attachment}", [AttachmentController::class, "download"])
        ->defaults("attachment_type", "attachment")
        ->name("attachments.download");
    Route::get("/{meeting}", MeetingShowComponent::class)->name("show");
    Route::get("/{meeting}/edit", MeetingEditComponent::class)->name("edit");
});

Route::middleware(["auth:web"])->prefix("/admin/stage-sessions")->name("admin.stage.sessions.")->group(function () {
    Route::get("/", function () {
        return redirect(tenant_routes("admin.stage.sessions.table"));
    })->name("index");
    Route::get("/table", SessionIndexComponent::class)->name("table");
    Route::get("/board", SessionIndexComponent::class)->name("board");
    Route::get("/create", SessionCreateComponent::class)->name("create");
    Route::get("/attachments/{attachment}", [AttachmentController::class, "download"])
        ->defaults("attachment_type", "attachment")
        ->name("attachments.download");
    Route::get("/{session}", SessionShowComponent::class)->name("show");
    Route::get("/{session}/edit", SessionEditComponent::class)->name("edit");
});
