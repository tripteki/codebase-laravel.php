<?php

namespace App\Exports\Settings\Profiles;

use Tripteki\SettingProfile\Models\Admin\Environment;
use Tripteki\SettingProfile\Http\Resources\EnvironmentResource;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class ProfileExport implements FromArray, ShouldAutoSize, WithStyles, WithHeadings
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

            "Variable",
            "Value",
        ];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return EnvironmentResource::collection(Environment::all([

            "variable",
            "value",

        ]))->resolve();
    }
};
