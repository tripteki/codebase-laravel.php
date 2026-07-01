<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use Illuminate\Validation\Rule;
use Modules\Acl\App\Enums\RoleEnum;
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
     * @param string|null $role
     * @return void
     */
    public function __construct(
        public ?string $name = null,
        public ?string $full_name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?string $password_confirmation = null,
        public ?string $role = null,
    ) {}

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            "name" => [ "sometimes", "nullable", "string", "min:1", "max:255", ],
            "full_name" => [ "sometimes", "nullable", "string", "max:255", ],
            "role" => [
                "sometimes",
                "nullable",
                "string",
                Rule::enum(RoleEnum::class),
            ],
            "email" => [
                "sometimes",
                "nullable",
                "string",
                "max:255",
                "email:rfc,filter",
            ],
            "password" => [ "bail", "sometimes", "nullable", "string", "min:8", "required_with:password_confirmation", ],
            "password_confirmation" => [ "bail", "nullable", "string", "required_with:password", "same:password", ],
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

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            "password.required_with" => __("user.auth.password_required"),
            "password_confirmation.required_with" => __("user.auth.password_confirmation_required"),
            "password_confirmation.same" => __("user.auth.password_mismatch"),
        ];
    }
}
