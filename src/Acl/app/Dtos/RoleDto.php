<?php

namespace Modules\Acl\App\Dtos;

use App\Support\AdminTenancySupport;
use Illuminate\Validation\Rule;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class RoleDto extends Data
{
    /**
     * @param string $name
     * @param string $guard_name
     * @param array<int, string>|null $permission_ids
     * @param string|null $tenant
     * @return void
     */
    public function __construct(
        public string $name,
        public string $guard_name,
        public ?array $permission_ids = null,
        public ?string $tenant = null,
    ) {}

    /**
     * @param ValidationContext $context
     * @return array<string, mixed>
     */
    public static function rules(ValidationContext $context): array
    {
        $isCentralAdmin = is_central();
        $guard = (string) ($context->payload["guard_name"] ?? GuardEnum::WEB->value);
        $tenantId = AdminTenancySupport::resolveTenantIdForValidation($context->payload);

        return [
            "tenant" => array_filter([
                $isCentralAdmin ? "required" : "prohibited",
                "string",
            ]),
            "name" => [
                "required",
                "string",
                "min:2",
                "max:64",
                Rule::unique(Role::class)->where(function ($query) use ($guard, $tenantId): void {
                    $query->where("guard_name", $guard);

                    if ($tenantId === null) {
                        $query->whereNull("tenant_id");
                    } else {
                        $query->where("tenant_id", $tenantId);
                    }
                }),
            ],
            "guard_name" => ["required", "string", Rule::in(array_column(GuardEnum::cases(), "value"))],
            "permission_ids" => ["nullable", "array"],
            "permission_ids.*" => [
                "string",
                Rule::exists(Permission::class, "id")->where(function ($query) use ($tenantId): void {
                    if ($tenantId === null) {
                        $query->whereNull("tenant_id");
                    } else {
                        $query->where("tenant_id", $tenantId);
                    }
                }),
            ],
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
