<?php

namespace App\Providers;

use App\Observers\ImportCompletedObserver;
use App\Observers\ExportCompletedObserver;
use App\Observers\ActivityObserver;
use Src\V1\Api\Log\Models\Activity;
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
        Import::observe(ImportCompletedObserver::class);
        Export::observe(ExportCompletedObserver::class);
        Activity::observe(ActivityObserver::class);
    }
}
