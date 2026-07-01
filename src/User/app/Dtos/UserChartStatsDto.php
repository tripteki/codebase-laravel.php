<?php

namespace Modules\User\App\Dtos;

use Spatie\LaravelData\Data;

class UserChartStatsDto extends Data
{
    /**
     * @param list<string> $labels
     * @param list<int> $series
     * @return void
     */
    public function __construct(
        public array $labels,
        public array $series,
    ) {}
}
