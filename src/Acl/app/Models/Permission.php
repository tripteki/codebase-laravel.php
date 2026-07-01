<?php

namespace Modules\Acl\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Modules\Log\App\Concerns\LogsAclModelActivity;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

class Permission extends SpatiePermission
{
    use HasUlids,
        LogsAclModelActivity;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @param string $name
     * @param string|null $guardName
     * @return \Spatie\Permission\Contracts\Permission
     */
    public static function findByName(string $name, ?string $guardName = null): PermissionContract
    {
        $guardName ??= Guard::getDefaultName(static::class);
        $registrar = app(PermissionRegistrar::class);

        if (! $registrar->teams) {
            return parent::findByName($name, $guardName);
        }

        $teamId = getPermissionsTeamId();
        $permission = static::getPermissions([
            "name" => $name,
            "guard_name" => $guardName,
        ])->first(function (PermissionContract $candidate) use ($teamId): bool {
            $tenantId = $candidate->getAttribute("tenant_id");

            if ($teamId === null || $teamId === "") {
                return $tenantId === null || $tenantId === "";
            }

            return (string) $tenantId === (string) $teamId;
        });

        if (! $permission instanceof PermissionContract) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }

        return $permission;
    }
}
