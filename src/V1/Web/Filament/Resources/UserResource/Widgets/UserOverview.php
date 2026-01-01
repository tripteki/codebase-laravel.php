<?php

namespace Src\V1\Web\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserOverview extends ChartWidget
{
    /**
     * @var int|string|array
     */
    protected int | string | array $columnSpan = "full";

    /**
     * @var string|null
     */
    public ?string $filter = "week";

    /**
     * @return string
     */
    protected function getType(): string
    {
        return "line";
    }

    /**
     * @return array|null
     */
    protected function getFilters(): ?array
    {
        return [

            "week" => __("module.user.widgets.filters.week"),
            "month" => __("module.user.widgets.filters.month"),
        ];
    }

    /**
     * @return array
     */
    protected function getData(): array
    {
        $filtered = $this->filter;

        switch ($filtered) {

            case "week":
                $dates = collect(range(0, 6))->mapWithKeys(fn (int $date): array => [ ($difference = now()->subDays($date))->format("d") => $difference->format("Y-m-d"), ])->reverse();
                break;

            case "month":
                $dates = collect(range(0, 29))->mapWithKeys(fn (int $date): array => [ ($difference = now()->subDays($date))->format("m-d") => $difference->format("Y-m-d"), ])->reverse();
                break;
        }

        return [

            "labels" => $dates->keys(),

            "datasets" => [

                [
                    "label" => __("module.user.widgets.labels.activates"),
                    "borderColor" => "#0ea5e9",
                    "backgroundColor" => "#0ea5e9",
                    "tension" => 0.3,
                    "data" => $dates->values()->map(fn ($date): string => User::activated()->whereDate("created_at", $date)->count())->values(),
                ],

                [
                    "label" => __("module.user.widgets.labels.deactivates"),
                    "borderColor" => "#ef4444",
                    "backgroundColor" => "#ef4444",
                    "tension" => 0.3,
                    "data" => $dates->values()->map(fn ($date): string => User::deactivated()->whereDate("deleted_at", $date)->count())->values(),
                ],
            ],
        ];
    }
}
