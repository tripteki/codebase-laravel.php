<?php

namespace App\Exports\Settings\Menus;

use Tripteki\SettingMenu\Models\Admin\Detailmenu;
use Tripteki\SettingMenu\Http\Resources\MenudetailResource;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class MenuExport implements FromArray, ShouldAutoSize, WithStyles, WithHeadings
{
    /**
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
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

            "Bar",
            "Menu",
            "Category",
            "Icon",
            "Title",
            "Description",
        ];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return MenudetailResource::collection(Detailmenu::all())->resolve();
    }
};
