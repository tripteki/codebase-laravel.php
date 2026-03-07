<?php

namespace App\Exports\Admin\Event;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EventExport implements FromArray, WithHeadings
{
    /**
     * @var array
     */
    protected array $rows;

    /**
     * @param array $rows
     * @return void
     */
    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->rows;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $headings = [
            __("module_event.slug"),
            __("module_event.title"),
        ];
        if (config("tenancy.type") !== "path") {
            $headings[] = __("module_event.domains");
        }
        return array_merge($headings, [
            __("module_event.key_visual_primary_color"),
            __("module_event.key_visual_secondary_color"),
            __("module_event.key_visual_tertiary_color"),
            __("module_event.start_date"),
            __("module_event.start_time"),
            __("module_event.end_date"),
            __("module_event.end_time"),
            __("module_event.add_ons_features"),
            __("module_event.add_ons_modules"),
            __("module_event.updated_at"),
        ]);
    }
}
