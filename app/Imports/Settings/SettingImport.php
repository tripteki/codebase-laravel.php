<?php

namespace App\Imports\Settings;

use Tripteki\Setting\Contracts\Repository\Admin\ISettingRepository as ISettingAdminRepository;
use App\Http\Requests\Admin\Settings\SettingStoreValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class SettingImport implements ToCollection, WithStartRow
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
    protected function validate(Collection $rows)
    {
        $validator = (new SettingStoreValidation())->rules();

        Validator::make($rows->toArray(), [

            "*.0" => $validator["key"],
            "*.1" => "required",

        ])->validate();
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        $this->validate($rows);

        $settingAdminRepository = app(ISettingAdminRepository::class);

        foreach ($rows as $row) {

            $settingAdminRepository->create([

                "key" => $row[0],
                "value" => $row[1],
            ]);
        }
    }
};
