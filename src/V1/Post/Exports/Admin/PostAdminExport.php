<?php

namespace Src\V1\Post\Exports\Admin;

use Src\V1\Post\Repositories\Admin\PostAdminRepository;
use Src\V1\Post\Services\Admin\PostAdminService;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;

class PostAdminExport implements FromArray, ShouldAutoSize, WithStyles, WithHeadings
{
    /**
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        return [

            1 => [ "font" => [ "bold" => true, ], ],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [

            "Identifier",
            "Content",
            "Created_at",
            "Updated_at",
            "Deleted_at",
            "User",
        ];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $postAdminRepository = new PostAdminRepository();
        $postAdminService = new PostAdminService($postAdminRepository);

        return $postAdminService->select()->getData()->data;
    }
};
