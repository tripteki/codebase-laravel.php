<?php

namespace Src\V1\Web\Providers\Filament;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {

            $switch->
                visible(outsidePanels: true)->
                locales([

                    "en",
                    "id",
                ]);
        });
    }
}
