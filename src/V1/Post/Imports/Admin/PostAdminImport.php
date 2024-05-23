<?php

namespace Src\V1\Post\Imports\Admin;

use Src\V1\Post\Repositories\Admin\PostAdminRepository;
use Src\V1\Post\Services\Admin\PostAdminService;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class PostAdminImport implements ToCollection, WithStartRow
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
        $postAdminRepository = new PostAdminRepository();
        $postAdminService = new PostAdminService($postAdminRepository);

        foreach ($rows as $row) {

            $postAdminService->store([

                "content" => $row[0],
                "user_id" => $row[1],
            ]);
        }
    }
};
