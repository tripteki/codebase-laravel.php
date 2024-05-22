<?php

namespace App\Http\Requests\Settings\Menus;

use Illuminate\Validation\Rule;
use Tripteki\SettingMenu\Scopes\MenuStrictScope;
use Tripteki\SettingMenu\Models\Admin\Detailmenu;
use Tripteki\Helpers\Http\Requests\FormValidation;
use Illuminate\Support\Str;

class MenuStoreValidation extends FormValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return [

            "menu" => Str::of($this->input("menu"))->explode(".")->map(fn ($menu, $key) => MenuStrictScope::space($this->input("bar").".".$menu))->toArray(),
        ];
    }

    /**
     * @return void
     */
    protected function postValidation()
    {
        return [

            "menu" => collect($this->input("menu"))->map(fn ($menu, $key) => Str::replaceFirst(MenuStrictScope::space($this->input("bar")."."), "", $menu))->implode("."),
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

            "menu" => "required|array",
            "menu.*" => [

                Rule::exists(Detailmenu::class, "menuable_id"),
            ],
        ];
    }
};
