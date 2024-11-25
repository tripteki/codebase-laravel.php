<?php

namespace Src\V1\Api\User\Dtos;

use App\Models\User;
use Src\V1\Web\Filament\Resources\UserResource\Forms\UserForm;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Illuminate\Support\Facades\Auth;

class UserDto extends Data
{
    /**
     * @param string|null $id
     * @param string $name
     * @param string $email
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
        //
    }

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        $user = @User::find($context->payload["id"]);

        return [

            "name" => UserForm::validation("name", $user),
            "email" => UserForm::validation("email", $user),
            "password" => UserForm::validation("password"),
        ];
    }

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return bool
     */
    public static function authorize(ValidationContext $context): bool
    {
        return true;
    }
}
