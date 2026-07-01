<?php

namespace Modules\Acl\App\Dtos;

use Modules\Acl\App\Models\Permission;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class PermissionIdentifierDto extends Data
{
    #[FromRouteParameter("id")]
    #[Exists(Permission::class)]
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
