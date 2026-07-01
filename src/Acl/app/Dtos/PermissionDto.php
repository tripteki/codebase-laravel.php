<?php

namespace Modules\Acl\App\Dtos;

use App\Support\AdminTenancySupport;
use Illuminate\Validation\Rule;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Models\Permission;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class PermissionDto extends Data
{
    /**
     * @param string $name
     * @param string $guard_name
     * @param string|null $tenant
     * @return void
     */
    public function __construct(
        public string $name,
        public string $guard_name,
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
                "max:128",
                Rule::unique(Permission::class)->where(function ($query) use ($guard, $tenantId): void {
                    $query->where("guard_name", $guard);

                    if ($tenantId === null) {
                        $query->whereNull("tenant_id");
                    } else {
                        $query->where("tenant_id", $tenantId);
                    }
                }),
            ],
            "guard_name" => ["required", "string", Rule::in(array_column(GuardEnum::cases(), "value"))],
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
