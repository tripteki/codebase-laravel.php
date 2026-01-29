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
     * Create a new export instance.
     *
     * @param array $users
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
        return [
            "ID",
            "Name",
            "Email",
            "Email Verified At",
            "Created At",
            "Updated At",
        ];
    }
}
