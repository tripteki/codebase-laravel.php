<?php

namespace App\Exports\Admin\Role;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RoleExport implements FromArray, WithHeadings
{
    /**
     * @var array
     */
    protected array $roles;

    /**
     * @param array $roles
     * @return void
     */
    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->roles;
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
            "Permissions",
            "Created At",
            "Updated At",
        ];
    }
}
