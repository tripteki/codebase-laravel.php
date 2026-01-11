<?php

namespace App\Providers;

use App\Observers\ImportCompletedObserver;
use App\Observers\ExportCompletedObserver;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Import::observe(ImportCompletedObserver::class);
        Export::observe(ExportCompletedObserver::class);
    }
}
