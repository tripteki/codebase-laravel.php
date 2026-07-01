<?php

namespace Modules\Acl\App\Dtos;

use Carbon\Carbon;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Acl\App\Support\AclGuard;
use Spatie\LaravelData\Data;

class RoleTransformerDto extends Data
{
    /**
     * @param string $id
     * @param string $name
     * @param string $guard_name
     * @param array<int, array{id: string, name: string, guard_name: string}> $permissions
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
        public array $permissions,
        public bool $is_protected,
        public ?\DateTimeInterface $created_at,
        public ?\DateTimeInterface $updated_at,
        public ?string $tenant_id = null,
    ) {}

    /**
     * @param Role $role
     * @param bool $withPermissions
     * @return self
     */
    public static function fromRole(Role $role, bool $withPermissions = true): self
    {
        $permissions = [];

        if ($withPermissions && $role->relationLoaded("permissions")) {
            $permissions = $role->permissions
                ->map(fn (Permission $permission) => [
                    "id" => (string) $permission->getKey(),
                    "name" => $permission->name,
                    "guard_name" => $permission->guard_name,
                ])
                ->values()
                ->all();
        }

        return new self(
            id: (string) $role->getKey(),
            name: $role->name,
            guard_name: $role->guard_name,
            permissions: $permissions,
            is_protected: AclGuard::isProtectedRole($role),
            created_at: self::castDate($role->created_at),
            updated_at: self::castDate($role->updated_at),
            tenant_id: $role->tenant_id !== null ? (string) $role->tenant_id : null,
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
