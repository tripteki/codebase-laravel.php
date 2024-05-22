<?php

namespace App\Http\Requests\Admin\Settings\Menus;

use Illuminate\Validation\Rule;
use Tripteki\SettingMenu\Scopes\MenuStrictScope;
use Tripteki\SettingMenu\Models\Admin\Detailmenu;
use Tripteki\Helpers\Http\Requests\FormValidation;
use Illuminate\Support\Str;

class MenuUpdateValidation extends FormValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return [

            "bar" => $this->route("bar"),
            "menu" => MenuStrictScope::space($this->route("bar").".".$this->route("menu")),
        ];
    }

    /**
     * @return void
     */
    protected function postValidation()
    {
        return [

            "menu" => Str::replaceFirst(MenuStrictScope::space($this->route("bar")."."), "", $this->route("menu")),
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

            "bar" => "required|string|in:headernavbar,sidenavbar",

            "menu" => [

                "required",
                "string",
                "max:127",
                "regex:/^[a-z\._\-]+$/",
                Rule::exists(Detailmenu::class, "menuable_id"),
            ],

            "category" => "sometimes|string|nullable|max:127",
            "icon" => "required|string|lowercase|max:127|regex:/^[a-zA-Z_\-]+$/",
            "title" => "required|string|max:127",
            "description" => "required|string|max:65535",
        ];
    }
};
