<?php

namespace App\Imports\Admin\Event;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EventImport implements ToArray, WithHeadingRow
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
