<?php

namespace App\Dtos;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;

class OffsetPaginationDto extends Data
{
    /**
     * @param int $totalPage
     * @param int $perPage
     * @param int $currentPage
     * @param int|null $nextPage
     * @param int|null $previousPage
     * @param int $firstPage
     * @param int $lastPage
     * @param array<int, mixed> $data
     * @return void
     */
    public function __construct(
        public int $totalPage,
        public int $perPage,
        public int $currentPage,
        public ?int $nextPage,
        public ?int $previousPage,
        public int $firstPage,
        public int $lastPage,
        public array $data,
    ) {}

    /**
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
     * @param array<int, mixed> $data
     * @return self
     */
    public static function fromPaginator(LengthAwarePaginator $paginator, array $data): self
    {
        $currentPage = $paginator->currentPage();
        $lastPage = max(1, $paginator->lastPage());

        return new self(
            totalPage: $lastPage,
            perPage: $paginator->perPage(),
            currentPage: $currentPage,
            nextPage: $currentPage < $lastPage ? $currentPage + 1 : null,
            previousPage: $currentPage > 1 ? $currentPage - 1 : null,
            firstPage: 1,
            lastPage: $lastPage,
            data: $data,
        );
    }
}
