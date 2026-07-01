<?php

namespace Tests;

use App\Models\User;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Event\App\Models\Event;
use Modules\Event\App\Services\EventBootstrapService;

trait InteractsWithTenancy
{
    /**
     * @var string|null
     */
    protected ?string $tenantId = null;

    /**
     * @return void
     */
    protected function enablePermissionTeams(): void
    {
        config([
            "permission.teams" => true,
        ]);
    }

    /**
     * @param User $user
     * @param string $role
     * @return void
     */
    protected function assignCentralRole(User $user, string $role): void
    {
        sync_permissions_team_context();
        $user->assignRole($role);
    }

    /**
     * @param User $user
     * @param string $role
     * @param string $tenantId
     * @return void
     */
    protected function assignTenantRole(User $user, string $role, string $tenantId): void
    {
        tenancy()->initialize(Event::query()->findOrFail($tenantId));
        sync_permissions_team_context();

        try {
            $user->assignRole($role);
        } finally {
            tenancy()->end();
            sync_permissions_team_context();
        }
    }

    /**
     * @param string $id
     * @param array<string, mixed> $attributes
     * @return Event
     */
    protected function createTenantEvent(string $id, array $attributes = []): Event
    {
        $event = new Event;
        $event->setAttribute("id", $id);

        foreach ($attributes as $key => $value) {
            $event->setAttribute($key, $value);
        }

        $event->save();

        app(EventBootstrapService::class)->bootstrap(
            Event::query()->findOrFail($id),
        );

        return Event::query()->findOrFail($id);
    }

    /**
     * @param string $tenantId
     * @param callable(): mixed $callback
     * @return mixed
     */
    protected function withinTenant(string $tenantId, callable $callback): mixed
    {
        tenancy()->initialize(Event::query()->findOrFail($tenantId));
        sync_permissions_team_context();

        try {
            return $callback();
        } finally {
            tenancy()->end();
            sync_permissions_team_context();
        }
    }

    /**
     * @param string $name
     * @return Permission
     */
    protected function centralPermission(string $name): Permission
    {
        sync_permissions_team_context();

        return Permission::query()
            ->where("name", $name)
            ->whereNull("tenant_id")
            ->firstOrFail();
    }

    /**
     * @param string $name
     * @return Permission
     */
    protected function tenantPermission(string $name): Permission
    {
        return $this->withinTenant($this->tenantId ?? "", fn () => Permission::query()
            ->where("name", $name)
            ->firstOrFail());
    }

    /**
     * @param string $name
     * @return Role
     */
    protected function tenantRole(string $name): Role
    {
        return $this->withinTenant($this->tenantId ?? "", fn () => Role::query()
            ->where("name", $name)
            ->firstOrFail());
    }

    /**
     * @param string $path
     * @return string
     */
    protected function adminApi(string $path = ""): string
    {
        $prefix = admin_api_prefix($this->tenantId);
        $normalized = $path === "" ? "" : (str_starts_with($path, "/") ? $path : "/{$path}");

        return "{$prefix}{$normalized}";
    }
}
