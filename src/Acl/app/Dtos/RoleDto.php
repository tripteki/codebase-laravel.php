<?php

namespace Modules\Acl\App\Dtos;

use App\Support\AdminTenancySupport;
use Illuminate\Validation\Rule;
use Modules\Acl\App\Enums\GuardEnum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class RoleDto extends Data
{
    /**
     * @param string $name
     * @param string|null $guard_name
     * @param array<int, string>|null $permission_ids
     * @param string|null $tenant
     * @return void
     */
    public function __construct(
        public string $name,
        public ?string $guard_name = "web",
        public ?array $permission_ids = null,
        public ?string $tenant = null,
    ) {}

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array<string, mixed>
     */
    public static function rules(ValidationContext $context): array
    {
        $payloadHasTenant = AdminTenancySupport::payloadHasTenant($context->payload);

        return [
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
            "guard_name" => ["sometimes", "nullable", "string", Rule::in(array_column(GuardEnum::cases(), "value"))],
            "permission_ids" => ["nullable", "array"],
            "permission_ids.*" => ["string"],
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
