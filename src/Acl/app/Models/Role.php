<?php

namespace Modules\Acl\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Modules\Log\App\Concerns\LogsAclModelActivity;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

class Role extends SpatieRole
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
     * @param array<string, mixed> $params
     * @return \Spatie\Permission\Contracts\Role|null
     */
    protected static function findByParam(array $params = []): ?RoleContract
    {
        $query = static::query();
        $registrar = app(PermissionRegistrar::class);

        if ($registrar->teams) {
            $teamsKey = $registrar->teamsKey;
            $teamId = array_key_exists($teamsKey, $params) ? $params[$teamsKey] : getPermissionsTeamId();

            if ($teamId === null || $teamId === "") {
                $query->where(fn ($nested) => $nested->whereNull($teamsKey)->orWhere($teamsKey, ""));
            } else {
                $query->where($teamsKey, $teamId);
            }

            unset($params[$teamsKey]);
        }

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }
}
