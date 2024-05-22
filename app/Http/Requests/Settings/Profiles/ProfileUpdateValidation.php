<?php

namespace App\Http\Requests\Settings\Profiles;

use Tripteki\SettingProfile\Scopes\ProfileStrictScope;
use Tripteki\SettingProfile\Models\Admin\Environment;
use Tripteki\Helpers\Http\Requests\FormValidation;
use Illuminate\Support\Str;

class ProfileUpdateValidation extends FormValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return [

            "variable" => ProfileStrictScope::space($this->route("variable")),
        ];
    }

    /**
     * @return void
     */
    protected function postValidation()
    {
        return [

            "variable" => Str::replaceFirst(ProfileStrictScope::space(null), "", $this->route("variable")),
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

            "variable" => "required|string|lowercase|exists:".Environment::class.",variable",
            "value" => "required|string|max:65535",
        ];
    }
};
