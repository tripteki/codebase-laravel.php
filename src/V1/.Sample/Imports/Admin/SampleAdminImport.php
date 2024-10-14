<?php

namespace Src\V1\Sample\Imports\Admin;

use Src\V1\Sample\Repositories\Admin\SampleAdminRepository;
use Src\V1\Sample\Services\Admin\SampleAdminService;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class SampleAdminImport implements ToCollection, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        $sampleAdminRepository = new SampleAdminRepository();
        $sampleAdminService = new SampleAdminService($sampleAdminRepository);

        foreach ($rows as $row) {

            $sampleAdminService->store([

                "content" => $row[1],
                "user_id" => (@json_decode($row[5], true))["id"],
            ]);
        }
    }
};
