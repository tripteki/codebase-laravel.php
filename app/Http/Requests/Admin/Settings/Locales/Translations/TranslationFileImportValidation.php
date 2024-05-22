<?php

namespace App\Http\Requests\Admin\Settings\Locales\Translations;

use Tripteki\Helpers\Http\Requests\FileImportValidation;

class TranslationFileImportValidation extends FileImportValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return array_merge(parent::preValidation(), [

            "code" => $this->route("code"),
        ]);
    }

    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules()
    {
        $validator = (new TranslationStoreValidation())->rules();

        return array_merge(parent::rules(), [

            "code" => $validator["code"],
        ]);
    }
};
