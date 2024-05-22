<?php

namespace App\Http\Requests\Settings;

use Tripteki\Setting\Models\Setting;
use Illuminate\Validation\Rule;
use Tripteki\Helpers\Contracts\AuthModelContract;
use Tripteki\Helpers\Http\Requests\FormValidation;
use Illuminate\Support\Facades\Auth;

class SettingDestroyValidation extends FormValidation
{
    /**
     * @return string
     */
    public function keyid()
    {
        return foreignKeyName(app(AuthModelContract::class));
    }

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

            "key" => [

                "required",
                "string",
                Rule::exists(Setting::class, "key")->where(function ($query) {

                    return $query->where($this->keyid(), Auth::id());
                }),
            ],
        ];
    }
};
