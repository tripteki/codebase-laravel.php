<?php

namespace App\Exports\Admin\Stage;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SessionExport implements FromArray, WithHeadings
{
    /**
     * @param array $sessions
     * @return void
     */
    public function __construct(
        protected array $sessions
    ) {
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->sessions;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $headings = [
            __("module_stage.column_id"),
        ];

        if (config("tenancy.is_tenancy")) {
            $headings[] = "Tenant ID";
        }

        return array_merge($headings, [
            __("module_stage.column_title"),
            __("module_stage.column_description"),
            __("module_stage.column_start_at"),
            __("module_stage.column_end_at"),
            __("module_stage.speakers"),
        ]);
    }
}
