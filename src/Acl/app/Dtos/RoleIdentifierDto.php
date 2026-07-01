<?php

namespace Modules\Acl\App\Dtos;

use Modules\Acl\App\Models\Role;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class RoleIdentifierDto extends Data
{
    #[FromRouteParameter("id")]
    #[Exists(Role::class)]
    /**
     * @var string|null
     */
    public ?string $id;

    /**
     * @return bool
     */
    public static function authorize(): bool
    {
        return true;
    }
}
