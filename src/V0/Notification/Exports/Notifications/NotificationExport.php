<?php

namespace Src\V0\Notification\Exports\Notifications;

use Illuminate\Notifications\DatabaseNotification as NotificationModel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class NotificationExport implements FromCollection, ShouldAutoSize, WithStyles, WithHeadings
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

            "ID",
            "Type",
            "Read At",
            "Updated At",
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function collection()
    {
        return NotificationModel::all([

            "id",
            "type",
            "read_at",
            "updated_at",
        ]);
    }
};
