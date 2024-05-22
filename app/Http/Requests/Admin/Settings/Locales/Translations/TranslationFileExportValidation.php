<?php

namespace App\Http\Requests\Admin\Settings\Locales\Translations;

use Tripteki\Helpers\Http\Requests\FileExportValidation;

class TranslationFileExportValidation extends FileExportValidation
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
        $validator = (new TranslationShowValidation())->rules();

        return array_merge(parent::rules(), [

            "code" => $validator["code"],
        ]);
    }
};
