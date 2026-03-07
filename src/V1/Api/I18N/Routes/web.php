<?php

use Src\V1\Api\I18N\Http\Controllers\I18NController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

Route::get("i18n/{lang}", [ I18NController::class, "update", ])->name("i18n");
