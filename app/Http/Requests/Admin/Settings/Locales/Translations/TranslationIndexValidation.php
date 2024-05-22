<?php

namespace App\Http\Requests\Admin\Settings\Locales\Translations;

use Tripteki\SettingLocale\Models\Admin\Language;
use Tripteki\Helpers\Http\Requests\FormValidation;

class TranslationIndexValidation extends FormValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return [

            "code" => $this->route("code"),
        ];
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
        return [

            "code" => "required|string|exists:".Language::class.",code",
        ];
    }
};
