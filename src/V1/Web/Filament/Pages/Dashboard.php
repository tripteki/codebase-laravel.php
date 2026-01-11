<?php

namespace Src\V1\Web\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Src\V1\Web\Filament\Resources\UserResource\Widgets\UserOverview;

class Dashboard extends BaseDashboard
{
    /**
     * @return array<int, string>
     */
    public function getWidgets(): array
    {
        return [

            UserOverview::class,
        ];
    }
}
