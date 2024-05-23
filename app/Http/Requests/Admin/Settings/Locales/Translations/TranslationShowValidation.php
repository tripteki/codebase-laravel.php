<?php

namespace App\Http\Requests\Admin\Settings\Locales\Translations;

use Illuminate\Validation\Rule;
use Tripteki\SettingLocale\Models\Admin\Language;
use Tripteki\SettingLocale\Models\Admin\Translation;
use Tripteki\Helpers\Http\Requests\FormValidation;

class TranslationShowValidation extends FormValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return [

            "code" => $this->route("code"),
            "key" => $this->route("key"),
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
                Rule::exists(Translation::class, "key")->where(function ($query) {

                    return $query->where("language_code", $this->route("code"));
                }),
            ],
        ];
    }
};
