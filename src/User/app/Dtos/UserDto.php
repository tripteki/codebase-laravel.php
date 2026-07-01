<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use App\Support\AdminTenancySupport;
use Illuminate\Support\Facades\Auth;
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
            "tenant" => array_filter([
                $payloadHasTenant ? "required" : "prohibited",
                "string",
            ]),

            "name" => [
                "required",
                "string",
                "min:2",
                "max:16",
            ],
            "full_name" => [
                "nullable",
                "string",
                "max:255",
            ],
            "email" => [
                "required",
                "string",
                "min:8",
                "max:48",
                "email",
                UserDefaultsHelper::uniqueEmailRule($tenantId, $user),
            ],
            "password" => array_merge(
                $passwordless ? [ "nullable", ] : [ "required", ],
                [ "string", "min:8", "max:16", ],
            ),
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
}
