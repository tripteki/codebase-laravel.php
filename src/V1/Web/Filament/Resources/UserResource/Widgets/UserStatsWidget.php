<?php

namespace Src\V1\Web\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    /**
     * @var int|string|array
     */
    protected int | string | array $columnSpan = "full";

    /**
     * @return array
     */
    protected function getStats(): array
    {
        return [
            Stat::make(__("module.user.widgets.stats.total"), User::count())
                ->description(__("module.user.widgets.stats.total_description"))
                ->descriptionIcon("heroicon-o-user-group")
                ->color("primary")
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make(__("module.user.widgets.stats.active"), User::activated()->count())
                ->description(__("module.user.widgets.stats.active_description"))
                ->descriptionIcon("heroicon-m-check-circle")
                ->color("success")
                ->chart([3, 5, 4, 6, 7, 8, 9]),

            Stat::make(__("module.user.widgets.stats.inactive"), User::deactivated()->count())
                ->description(__("module.user.widgets.stats.inactive_description"))
                ->descriptionIcon("heroicon-m-x-circle")
                ->color("danger")
                ->chart([9, 8, 7, 6, 5, 4, 3]),

            Stat::make(__("module.user.widgets.stats.new_today"), User::whereDate("created_at", today())->count())
                ->description(__("module.user.widgets.stats.new_today_description"))
                ->descriptionIcon("heroicon-m-arrow-trending-up")
                ->color("info")
                ->chart([2, 3, 5, 4, 6, 7, 5]),
        ];
    }
}

