<?php

namespace Tests\Unit;

use App\Dtos\OffsetPaginationDto;
use App\Support\QueryParser;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ApiResponseFormatTest extends TestCase
{
    /**
     * @return void
     */
    public function test_offset_pagination_dto_matches_fastapi_shape(): void
    {
        $paginator = new LengthAwarePaginator(
            items: [ [ "id" => "1", ], [ "id" => "2", ], ],
            total: 25,
            perPage: 10,
            currentPage: 2,
        );

        $dto = OffsetPaginationDto::fromPaginator($paginator, [ [ "id" => "1", ], [ "id" => "2", ], ]);

        $this->assertSame(3, $dto->totalPage);
        $this->assertSame(10, $dto->perPage);
        $this->assertSame(2, $dto->currentPage);
        $this->assertSame(3, $dto->nextPage);
        $this->assertSame(1, $dto->previousPage);
        $this->assertSame(1, $dto->firstPage);
        $this->assertSame(3, $dto->lastPage);
        $this->assertCount(2, $dto->data);
    }

    /**
     * @return void
     */
    public function test_query_parser_orders_and_filters(): void
    {
        $this->assertSame([
            [ "field" => "created_at", "direction" => "desc", ],
            [ "field" => "name", "direction" => "asc", ],
        ], QueryParser::parseOrders("created_at:desc,name:asc"));

        $this->assertSame([
            [ "field" => "name", "search" => "john", ],
            [ "field" => "email", "search" => "test", ],
        ], QueryParser::parseFilters("name:john,email:test"));
    }
}
