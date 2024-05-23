<?php

namespace App\Http\Requests\Admin\Settings\Locales\Translations;

use Illuminate\Validation\Rule;
use Tripteki\SettingLocale\Models\Admin\Language;
use Tripteki\SettingLocale\Models\Admin\Translation;
use Tripteki\Helpers\Http\Requests\FormValidation;

class TranslationStoreValidation extends FormValidation
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

            "key" => [

                "required",
                "string",
                "lowercase",
                "max:127",
                "regex:/^[a-zA-Z\.]+$/",
                Rule::unique(Translation::class, "key")->where(function ($query) {

                    return $query->where("language_code", $this->route("code"));
                }),
            ],

            "translate" => "required|string|max:65535",
        ];
    }
};
