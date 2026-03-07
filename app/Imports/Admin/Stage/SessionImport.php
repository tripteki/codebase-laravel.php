<?php

namespace App\Imports\Admin\Stage;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SessionImport implements ToArray, WithHeadingRow
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
