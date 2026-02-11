<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebPushSubscriptionController;

require __DIR__."/web.manifest.php";
require __DIR__."/../src/V1/Api/I18N/Routes/web.php";
require __DIR__."/../src/V1/Api/Auth/Routes/web.php";

Route::get("/login", function () { return redirect("/admin/login"); })->name("login");
Route::get("/home", function () { return redirect("/admin/dashboard"); })->name("home");

Route::middleware([ "i18n", ])->group(function () {

    Route::get("/", function () { return view("livewire.index"); });

    require __DIR__."/admin/auth.php";
    require __DIR__."/admin/search.php";
    require __DIR__."/admin/user.php";
    require __DIR__."/admin/permission.php";
    require __DIR__."/admin/role.php";
    require __DIR__."/admin/setting.php";
    require __DIR__."/admin/notification.php";

    Route::middleware(["auth:web"])->group(function () {

        Route::post("/webpush/subscribe", [ WebPushSubscriptionController::class, "store", ])->name("webpush.subscribe");
        Route::post("/webpush/unsubscribe", [ WebPushSubscriptionController::class, "destroy", ])->name("webpush.unsubscribe");
    });
});
