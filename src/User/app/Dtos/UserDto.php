<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use App\Support\AdminTenancySupport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Event\App\Support\AuthAddOnsHelper;
use Modules\User\App\Support\UserDefaultsHelper;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

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
     * @param string|null $full_name
     * @param string|null $role
     * @param string|null $tenant
     * @return void
     */
    public function __construct(
        public ?string $id,
        public string $name,
        public string $email,
        public ?string $password,
        public ?\DateTime $created_at,
        public ?\DateTime $updated_at,
        public ?\DateTime $deleted_at,
        public ?string $full_name = null,
        public ?string $role = null,
        public ?string $tenant = null,
    ) {}

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        $user = Auth::user();
        $tenantId = UserDefaultsHelper::resolveTenantIdForValidation($context->payload);
        $payloadHasTenant = AdminTenancySupport::payloadHasTenant($context->payload);

        if (is_string($tenantId) && trim($tenantId) !== "") {
            AuthAddOnsHelper::initializeFromTenantId($tenantId);
        } elseif ($user instanceof User) {
            AuthAddOnsHelper::initializeForUser($user);
        }

        $passwordless = AuthAddOnsHelper::isPasswordless();

        $validation = [
            "tenant" => is_tenancy()
                ? array_filter([
                    $payloadHasTenant ? "required" : "prohibited",
                    "string",
                ])
                : [ "prohibited", ],

            "name" => [
                "required",
                "string",
                "min:1",
                "max:255",
            ],
            "role" => [
                "nullable",
                "string",
                Rule::enum(RoleEnum::class),
            ],
            "full_name" => [
                "nullable",
                "string",
                "max:255",
            ],
            "email" => [
                "required",
                "string",
                "max:255",
                "email:rfc,filter",
            ],
            "password" => array_merge(
                $passwordless ? [ "nullable", ] : [ "required", ],
                [ "string", "min:8", ],
            ),
        ];

        $password = $context->payload["password"] ?? null;
        $needsConfirmation = ! $passwordless || (is_string($password) && $password !== "");

        if ($needsConfirmation) {
            $validation["password_confirmation"] = [
                "bail",
                "required",
                "string",
                "min:8",
                "same:password",
            ];
        }

        return $validation;
    }

    /**
     * @param array<string, mixed> $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        $name = trim((string) ($properties["name"] ?? ""));
        $fullName = trim((string) ($properties["full_name"] ?? ""));

        if ($name === "" && $fullName !== "") {
            $properties["name"] = $fullName;
        }

        return $properties;
    }

    /**
     * @return array<string, mixed>
     */
    public function createPayload(): array
    {
        $password = $this->password;

        if (($password === null || $password === "") && AuthAddOnsHelper::isPasswordless()) {
            $password = UserDefaultsHelper::defaultPassword();
        }

        return [
            "name" => $this->name,
            "email" => $this->email,
            "password" => $password,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function profilePayload(): array
    {
        $fullName = trim((string) ($this->full_name ?? $this->name));

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
            "password_confirmation.same" => __("user.auth.password_mismatch"),
        ];
    }
}
