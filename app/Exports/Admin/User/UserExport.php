<?php

namespace App\Exports\Admin\User;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromArray, WithHeadings
{
    /**
     * @var array
     */
    protected array $users;

    /**
     * @param array $users
     * @return void
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->users;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $headings = [
            "ID",
            "Full Name",
            "Name",
            "Email",
            "Roles",
        ];
        if (config("tenancy.is_tenancy")) {
            $headings[] = "Tenant ID";
        }
        return array_merge($headings, [
            "Email Verified At",
            "Created At",
            "Updated At",
        ]);
    }
}
