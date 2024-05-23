<?php

namespace App\Http\Requests\Admin\Settings;

use Tripteki\Setting\Models\Admin\Setting;
use Tripteki\Helpers\Http\Requests\FormValidation;

class SettingDestroyValidation extends FormValidation
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

            "key" => "required|string|exists:".Setting::class.",key",
        ];
    }
};
