<?php

namespace Modules\User\App\Dtos;

use Illuminate\Validation\Validator;
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
        return [
            "name" => [ "required", "string", "min:2", "max:255", ],
            "email" => [
                "required",
                "string",
                "email:rfc,filter",
                "max:255",
            ],
            "full_name" => [ "nullable", "string", "max:255", ],
            "interests" => [ "nullable", "array", ],
            "interests.*" => [ "string", ],
            "password" => [ "bail", "nullable", "string", "min:8", "max:16", ],
            "password_confirmation" => [ "bail", "nullable", "string", "min:8", "max:16", ],
        ];
    }

    /**
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public static function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $raw = json_decode((string) request()->getContent(), true);

            if (is_array($raw) && array_key_exists("password", $raw) && $raw["password"] === "") {
                $validator->errors()->add("password", __("validation.min.string", [
                    "attribute" => "password",
                    "min" => 8,
                ]));

                return;
            }

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $password = $validator->getData()["password"] ?? null;

            if (! is_string($password) || $password === "") {
                return;
            }

            $confirmation = $validator->getData()["password_confirmation"] ?? null;

            if (! is_string($confirmation) || $password !== $confirmation) {
                $validator->errors()->add("password_confirmation", __("user.auth.password_mismatch"));
            }
        });
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
