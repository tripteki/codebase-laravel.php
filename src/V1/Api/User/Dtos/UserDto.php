<?php

namespace Src\V1\Api\User\Dtos;

use App\Models\User;
use Src\V1\Web\Filament\Resources\UserResource\Forms\UserForm;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class UserDto extends Data
{
    #[Computed]
    /**
     * @var array
     */
    public array $can;

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
        $user = Auth::user();

        if ($user) {

            $this->can = [

                "update" => $user->can("update", User::class),
            ];
        }
    }

    /**
     * @return bool
     */
    public static function authorize(): bool
    {
        $user = Auth::user();

        if ($user) {

            return $user->can("update", User::class);
        }

        return true;
    }

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        $user = Auth::user();

        $validation = [

            "name" => UserForm::validation("name", $user),
            "email" => UserForm::validation("email", $user),
            "password" => UserForm::validation("password"),
        ];

        if (@$context->payload["password_confirmation"]) $validation["password_confirmation"] = UserForm::validation("password_confirmation");

        return $validation;
    }
}
