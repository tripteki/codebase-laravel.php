<?php

namespace Modules\Log\App\Listeners;

use App\Models\User;
use Modules\Log\App\Support\ActivityRecorder;
use Spatie\Permission\Events\PermissionAttached;
use Spatie\Permission\Events\PermissionDetached;
use Spatie\Permission\Events\RoleAttached;
use Spatie\Permission\Events\RoleDetached;

class PermissionAssignmentActivityListener
{
    /**
     * @param \Spatie\Permission\Events\RoleAttached $event
     * @return void
     */
    public function handleRoleAttached(RoleAttached $event): void
    {
        if (! $event->model instanceof User) {
            return;
        }

        ActivityRecorder::userRolesAttached(
            $event->model,
            ActivityRecorder::resolveRoleNames($event->rolesOrIds),
        );
    }

    /**
     * @param \Spatie\Permission\Events\RoleDetached $event
     * @return void
     */
    public function handleRoleDetached(RoleDetached $event): void
    {
        if (! $event->model instanceof User) {
            return;
        }

        ActivityRecorder::userRolesDetached(
            $event->model,
            ActivityRecorder::resolveRoleNames($event->rolesOrIds),
        );
    }

    /**
     * @param \Spatie\Permission\Events\PermissionAttached $event
     * @return void
     */
    public function handlePermissionAttached(PermissionAttached $event): void
    {
        if (! $event->model instanceof User) {
            return;
        }

        ActivityRecorder::userPermissionsAttached(
            $event->model,
            ActivityRecorder::resolvePermissionNames($event->permissionsOrIds),
        );
    }

    /**
     * @param \Spatie\Permission\Events\PermissionDetached $event
     * @return void
     */
    public function handlePermissionDetached(PermissionDetached $event): void
    {
        if (! $event->model instanceof User) {
            return;
        }

        ActivityRecorder::userPermissionsDetached(
            $event->model,
            ActivityRecorder::resolvePermissionNames($event->permissionsOrIds),
        );
    }
}
