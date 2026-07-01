<?php

namespace Modules\Acl\App\Dtos;

use Carbon\Carbon;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Support\AclGuard;
use Spatie\LaravelData\Data;

class PermissionTransformerDto extends Data
{
    /**
     * @param string $id
     * @param string $name
     * @param string $guard_name
     * @param bool $is_protected
     * @param \DateTimeInterface|null $created_at
     * @param \DateTimeInterface|null $updated_at
     * @param string|null $tenant_id
     * @return void
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $guard_name,
        public bool $is_protected,
        public ?\DateTimeInterface $created_at,
        public ?\DateTimeInterface $updated_at,
        public ?string $tenant_id = null,
    ) {}

    /**
     * @param Permission $permission
     * @return self
     */
    public static function fromPermission(Permission $permission): self
    {
        return new self(
            id: (string) $permission->getKey(),
            name: $permission->name,
            guard_name: $permission->guard_name,
            is_protected: AclGuard::isProtectedPermission($permission),
            created_at: self::castDate($permission->created_at),
            updated_at: self::castDate($permission->updated_at),
            tenant_id: $permission->tenant_id !== null ? (string) $permission->tenant_id : null,
        );
    }

    /**
     * @param mixed $value
     * @return \DateTimeInterface|null
     */
    private static function castDate(mixed $value): ?\DateTimeInterface
    {
        if ($value === null || $value === "") {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        return Carbon::parse($value);
    }
}
