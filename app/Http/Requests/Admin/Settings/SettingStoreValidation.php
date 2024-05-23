<?php

namespace App\Http\Requests\Admin\Settings;

use Tripteki\Setting\Models\Admin\Setting;
use Tripteki\Helpers\Http\Requests\FormValidation;

class SettingStoreValidation extends FormValidation
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

            "key" => "required|string|lowercase|max:255|unique:".Setting::class.",key",
            "value" => "required|string|max:65535",
        ];
    }
};
