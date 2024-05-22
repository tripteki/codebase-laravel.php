<?php

namespace App\Http\Requests\Settings;

use Tripteki\Helpers\Http\Requests\FormValidation;

class SettingUpdateValidation extends FormValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return [

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

            "key" => "required|string|lowercase|max:255|alpha",
            "value" => "required|string|max:65535",
        ];
    }
};
