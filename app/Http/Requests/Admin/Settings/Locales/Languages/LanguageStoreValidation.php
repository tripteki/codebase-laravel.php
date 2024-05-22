<?php

namespace App\Http\Requests\Admin\Settings\Locales\Languages;

use Tripteki\SettingLocale\Models\Admin\Language;
use Tripteki\Helpers\Http\Requests\FormValidation;

class LanguageStoreValidation extends FormValidation
{
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

            "code" => "required|string|lowercase|max:8|regex:/^[a-zA-Z_]+$/|unique:".Language::class.",code",
            "locale" => "required|string|max:127|regex:/^[a-z]+-[a-zA-Z]+$/",
        ];
    }
};
