<?php

namespace Modules\Acl\App\Dtos;

use Illuminate\Validation\Rule;
use Modules\Acl\App\Enums\GuardEnum;
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
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array<string, mixed>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            "name" => [
                "sometimes",
                "nullable",
                "string",
                "min:1",
                "max:255",
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
