<?php

namespace App\Imports\Admin\Role;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RoleImport implements ToArray, WithHeadingRow
{
    /**
     * @param array $array
     * @return array
     */
    public function array(array $array): array
    {
        return $array;
    }
}
