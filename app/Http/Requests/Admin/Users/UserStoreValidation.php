<?php

namespace App\Http\Requests\Admin\Users;

use Tripteki\Helpers\Contracts\AuthModelContract;
use Tripteki\Helpers\Http\Requests\FormValidation;
use Illuminate\Validation\Rules;

class UserStoreValidation extends FormValidation
{
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

            "name" => [ "required", "string", "max:15", "alpha", "unique:".get_class($provider).",name", ],
            "email" => [ "required", "string", "max:31", "email", "unique:".get_class($provider).",email", ],
            "password" => [ "required", "string", Rules\Password::defaults(), "confirmed", ],
        ];
    }
};
