<?php

namespace Modules\User\App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserAdminExport implements FromArray, WithHeadings, WithTitle
{
    /**
     * @param array<int, array<int, mixed>> $rows
     * @param array<int, string> $headings
     * @param string $sheetTitle
     * @return void
     */
    public function __construct(
        protected array $rows,
        protected array $headings,
        protected string $sheetTitle,
    ) {
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return $this->rows;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->sheetTitle;
    }
}
