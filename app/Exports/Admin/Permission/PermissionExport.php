<?php

namespace App\Exports\Admin\Permission;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PermissionExport implements FromArray, WithHeadings
{
    /**
     * @var array
     */
    protected array $permissions;

    /**
     * @param array $permissions
     * @return void
     */
    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->permissions;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            "ID",
            "Name",
            "Guard Name",
            "Created At",
            "Updated At",
        ];
    }
}
