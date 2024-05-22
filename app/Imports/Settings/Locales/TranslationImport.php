<?php

namespace App\Imports\Settings\Locales;

use Illuminate\Validation\Rule;
use Tripteki\SettingLocale\Models\Admin\Translation;
use Tripteki\SettingLocale\Contracts\Repository\Admin\ISettingLocaleTranslationRepository;
use App\Http\Requests\Admin\Settings\Locales\Translations\TranslationStoreValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class TranslationImport implements ToCollection, WithStartRow
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @param string $code
     * @return void
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

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
        $validator = (new TranslationStoreValidation())->rules();

        $key = $validator["key"];
        $translate = $validator["translate"];

        Validator::make($rows->toArray(), [

            "*.0" => array_replace($key, [

                (count($key) - 1) => Rule::unique(Translation::class, "key")->where(function ($query) {

                    return $query->where("language_code", $this->code);
                }),
            ]),
            "*.1" => $translate,

        ])->validate();
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        $this->validate($rows);

        $localeTranslationAdminRepository = app(ISettingLocaleTranslationRepository::class);

        foreach ($rows as $row) {

            $localeTranslationAdminRepository->create($this->code, [

                "key" => $row[0],
                "translate" => $row[1],
            ]);
        }
    }
};
