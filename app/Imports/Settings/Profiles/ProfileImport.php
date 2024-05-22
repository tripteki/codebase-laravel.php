<?php

namespace App\Imports\Settings\Profiles;

use Tripteki\SettingProfile\Scopes\ProfileStrictScope;
use Tripteki\SettingProfile\Contracts\Repository\Admin\ISettingProfileEnvironmentRepository;
use App\Http\Requests\Admin\Settings\Profiles\ProfileStoreValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ProfileImport implements ToCollection, WithStartRow
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
        $validator = (new ProfileStoreValidation())->rules();

        Validator::make($rows->map(function ($environment) {

            return [

                0 => ProfileStrictScope::space(@$environment[0]),
                1 => @$environment[1],
            ];

        })->toArray(), [

            "*.0" => $validator["variable"],
            "*.1" => $validator["value"],

        ])->validate();
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        $this->validate($rows);

        $profileAdminRepository = app(ISettingProfileEnvironmentRepository::class);

        foreach ($rows as $row) {

            $profileAdminRepository->create([

                "variable" => $row[0],
                "value" => $row[1],
            ]);
        }
    }
};
