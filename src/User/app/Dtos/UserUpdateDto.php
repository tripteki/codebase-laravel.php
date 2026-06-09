<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UserUpdateDto extends Data
{
    #[FromRouteParameter("id")]
    /**
     * @var string|null
     */
    public ?string $id = null;

    /**
     * @param string|null $name
     * @param string|null $full_name
     * @param string|null $email
     * @param string|null $password
     * @param string|null $password_confirmation
     * @return void
     */
    public function __construct(
        public ?string $name = null,
        public ?string $full_name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?string $password_confirmation = null,
    ) {}

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        $userId = $context->payload["id"] ?? null;

        return [
            "name" => [ "sometimes", "nullable", "string", "min:2", "max:16", ],
            "full_name" => [ "sometimes", "nullable", "string", "max:255", ],
            "email" => [
                "sometimes",
                "nullable",
                "string",
                "min:8",
                "max:48",
                "email",
                Rule::unique(User::class)->ignore($userId),
            ],
            "password" => [ "sometimes", "nullable", "string", "min:8", "max:16", ],
            "password_confirmation" => [ "nullable", "string", ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function updatePayload(): array
    {
        $payload = [];

        if ($this->name !== null) {
            $payload["name"] = $this->name;
        }

        if ($this->email !== null) {
            $payload["email"] = $this->email;
        }

        if ($this->password !== null && $this->password !== "") {
            $payload["password"] = $this->password;
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function profilePayload(): array
    {
        if ($this->full_name === null) {
            return [];
        }

        $fullName = trim($this->full_name);

        if ($fullName === "") {
            return [];
        }

        return [
            "full_name" => $fullName,
        ];
    }

    /**
     * @return bool
     */
    public static function authorize(): bool
    {
        return true;
    }
}
