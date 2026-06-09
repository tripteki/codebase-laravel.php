<?php

namespace App\Services;

use App\Dtos\OffsetPaginationDto;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Service
{
    /**
     * @template T
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
     * @param callable(mixed): T $mapper
     * @return \App\Dtos\OffsetPaginationDto
     */
    protected function toOffsetPagination(LengthAwarePaginator $paginator, callable $mapper): OffsetPaginationDto
    {
        $data = $paginator
            ->getCollection()
            ->map($mapper)
            ->values()
            ->all();

        return OffsetPaginationDto::fromPaginator($paginator, $data);
    }
}
