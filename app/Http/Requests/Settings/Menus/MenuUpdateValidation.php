<?php

namespace App\Http\Requests\Settings\Menus;

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

            "menu_from" => Str::of($this->input("menu_from"))->explode(".")->map(fn ($menu, $key) => MenuStrictScope::space($this->input("bar").".".$menu))->toArray(),
            "menu_to" => Str::of($this->input("menu_to"))->explode(".")->map(fn ($menu, $key) => MenuStrictScope::space($this->input("bar").".".$menu))->toArray(),
        ];
    }

    /**
     * @return void
     */
    protected function postValidation()
    {
        return [

            "menu_from" => collect($this->input("menu_from"))->map(fn ($menu, $key) => Str::replaceFirst(MenuStrictScope::space($this->input("bar")."."), "", $menu))->implode("."),
            "menu_to" => collect($this->input("menu_to"))->map(fn ($menu, $key) => Str::replaceFirst(MenuStrictScope::space($this->input("bar")."."), "", $menu))->implode("."),
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

            "menu_from" => "required|array",
            "menu_from.*" => [

                Rule::exists(Detailmenu::class, "menuable_id"),
            ],

            "menu_to" => "required|array",
            "menu_to.*" => [

                Rule::exists(Detailmenu::class, "menuable_id"),
            ],
        ];
    }
};
