<?php

namespace App\Http\Requests\API\Auth;

use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationStoreValidation extends FormRequest
{
    /**
     * Get the validation rules.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [

            "name" => [ "required", "string", "max:15", "alpha", "unique:".User::class.",name", ],
            "email" => [ "required", "string", "max:31", "email", "unique:".User::class.",email", ],
            "password" => [ "required", "string", Rules\Password::defaults(), "confirmed", ],
        ];
    }
}
