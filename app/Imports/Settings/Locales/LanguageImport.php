<?php

namespace App\Imports\Settings\Locales;

use Tripteki\SettingLocale\Contracts\Repository\Admin\ISettingLocaleLanguageRepository;
use App\Http\Requests\Admin\Settings\Locales\Languages\LanguageStoreValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class LanguageImport implements ToCollection, WithStartRow
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
        $validator = (new LanguageStoreValidation())->rules();

        Validator::make($rows->toArray(), [

            "*.0" => $validator["code"],
            "*.1" => $validator["locale"],

        ])->validate();
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        $this->validate($rows);

        $localeLanguageAdminRepository = app(ISettingLocaleLanguageRepository::class);

        foreach ($rows as $row) {

            $localeLanguageAdminRepository->create([

                "code" => $row[0],
                "locale" => $row[1],
            ]);
        }
    }
};
