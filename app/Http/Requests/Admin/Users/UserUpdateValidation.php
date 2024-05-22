<?php

namespace App\Http\Requests\Admin\Users;

use Tripteki\Helpers\Contracts\AuthModelContract;
use Tripteki\Helpers\Http\Requests\FormValidation;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserUpdateValidation extends FormValidation
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
            "name" => [ "required", "string", "max:15", "alpha", Rule::unique(get_class($provider))->ignore($this->route("identifier")), ],
            "email" => [ "required", "string", "max:31", "email", Rule::unique(get_class($provider))->ignore($this->route("identifier")), ],
            "password" => [ "required", "string", Rules\Password::defaults(), ],
        ];
    }
};
