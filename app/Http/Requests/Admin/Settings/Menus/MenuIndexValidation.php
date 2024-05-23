<?php

namespace App\Http\Requests\Admin\Settings\Menus;

use Tripteki\Helpers\Http\Requests\FormValidation;

class MenuIndexValidation extends FormValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return [

            "bar" => $this->route("bar"),
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
        ];
    }
};
