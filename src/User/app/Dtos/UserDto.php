<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Illuminate\Support\Facades\Auth;

class UserDto extends Data
{
    /**
     * @param string|null $id
     * @param string $name
     * @param string $email
     * @param string|null $password
     * @param \DateTime|null $created_at
     * @param \DateTime|null $updated_at
     * @param \DateTime|null $deleted_at
     * @return void
     */
    public function __construct(
        public ?string $id,
        public string $name,
        public string $email,
        public ?string $password,
        public ?\DateTime $created_at,
        public ?\DateTime $updated_at,
        public ?\DateTime $deleted_at
    ) {
    }

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        $user = Auth::user();

        $validation = [

            "name" => [
                "required",
                "string",
                "min:2",
                "max:16",
                Rule::unique(User::class)->ignore($user),
            ],
            "email" => [
                "required",
                "string",
                "min:8",
                "max:48",
                "email",
                Rule::unique(User::class)->ignore($user),
            ],
            "password" => [
                "required",
                "string",
                "min:8",
                "max:16",
            ],
        ];

        if (@$context->payload["password_confirmation"]) {
            $validation["password_confirmation"] = [
                "string",
                "same:password",
            ];
        }

        return $validation;
    }

    /**
     * @return bool
     */
    public static function authorize(): bool
    {
        return true;
    }
}
