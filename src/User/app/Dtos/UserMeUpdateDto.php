<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UserMeUpdateDto extends Data
{
    /**
     * @param string|null $name
     * @param string|null $email
     * @param string|null $full_name
     * @param array<int, string>|null $interests
     * @param string|null $password
     * @param string|null $password_confirmation
     * @return void
     */
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $full_name = null,
        public ?array $interests = null,
        public ?string $password = null,
        public ?string $password_confirmation = null,
    ) {}

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        $userId = Auth::id();

        return [
            "name" => [ "required", "string", "min:2", "max:255", ],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                Rule::unique(User::class)->ignore($userId),
            ],
            "full_name" => [ "nullable", "string", "max:255", ],
            "interests" => [ "nullable", "array", ],
            "interests.*" => [ "string", "max:100", ],
            "password" => [ "nullable", "string", "min:8", "max:16", ],
            "password_confirmation" => [ "nullable", "string", "same:password", "required_with:password", ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function userPayload(): array
    {
        $payload = [
            "name" => $this->name,
            "email" => $this->email,
        ];

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
        return [
            "full_name" => $this->full_name,
            "interests" => array_values(array_filter(array_map(
                static fn ($value) => trim((string) $value),
                $this->interests ?? [],
            ))),
        ];
    }
}
