<?php

namespace Modules\Acl\App\Dtos;

use Illuminate\Validation\Rule;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Models\Permission;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class PermissionUpdateDto extends Data
{
    #[FromRouteParameter("id")]
    /**
     * @var string|null
     */
    public ?string $id = null;

    /**
     * @param string|null $name
     * @param string|null $guard_name
     * @return void
     */
    public function __construct(
        public ?string $name = null,
        public ?string $guard_name = null,
    ) {}

    /**
     * @param ValidationContext $context
     * @return array<string, mixed>
     */
    public static function rules(ValidationContext $context): array
    {
        $permissionId = $context->payload["id"] ?? null;
        $guard = (string) ($context->payload["guard_name"] ?? GuardEnum::WEB->value);

        return [
            "name" => [
                "sometimes",
                "nullable",
                "string",
                "min:2",
                "max:128",
                Rule::unique(Permission::class)->where(fn ($query) => $query->where("guard_name", $guard))->ignore($permissionId),
            ],
            "guard_name" => ["sometimes", "nullable", "string", Rule::in(array_column(GuardEnum::cases(), "value"))],
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

        if ($this->guard_name !== null) {
            $payload["guard_name"] = $this->guard_name;
        }

        return $payload;
    }

    /**
     * @return bool
     */
    public static function authorize(): bool
    {
        return true;
    }
}
