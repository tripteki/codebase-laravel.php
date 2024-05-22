<?php

namespace App\Http\Requests\Admin\Users;

use Tripteki\Helpers\Contracts\AuthModelContract;
use Tripteki\Helpers\Http\Requests\FormValidation;

class UserDestroyValidation extends FormValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return [

            "identifier" => $this->route("identifier"),
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
        $provider = app(AuthModelContract::class);

        return [

            "identifier" => [ "required", "string", "exists:".get_class($provider).",".keyName($provider).",deleted_at,NULL", ],
        ];
    }
};
