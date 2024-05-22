<?php

namespace App\Http\Requests\Settings\Menus;

use Illuminate\Validation\Rule;
use Tripteki\SettingMenu\Scopes\MenuStrictScope;
use Tripteki\SettingMenu\Models\Admin\Detailmenu;
use Tripteki\Helpers\Http\Requests\FormValidation;
use Illuminate\Support\Str;

class MenuDestroyValidation extends FormValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return [

            "bar" => $this->route("bar"),
            "menu" => Str::of($this->route("menu"))->explode(".")->map(fn ($menu, $key) => MenuStrictScope::space($this->route("bar").".".$menu))->toArray(),
        ];
    }

    /**
     * @return void
     */
    protected function postValidation()
    {
        return [

            "menu" => collect($this->route("menu"))->map(fn ($menu, $key) => Str::replaceFirst(MenuStrictScope::space($this->route("bar")."."), "", $menu))->implode("."),
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
