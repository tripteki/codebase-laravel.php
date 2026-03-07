<?php

namespace Modules\User\App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserAdminImport implements ToArray, WithHeadingRow
{
    /**
     * @param array<int, array<string, mixed>> $array
     * @return array<int, array<string, mixed>>
     */
    public function array(array $array): array
    {
        return $array;
    }
}
