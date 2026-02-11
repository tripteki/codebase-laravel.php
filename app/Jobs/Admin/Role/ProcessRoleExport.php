<?php

namespace App\Jobs\Admin\Role;

use App\Jobs\Base\ProcessExportJob;
use App\Exports\Admin\Role\RoleExport;
use Src\V1\Api\Acl\Models\Role;

class ProcessRoleExport extends ProcessExportJob
{
    /**
     * Get the export class name.
     *
     * @return string
     */
    protected function getExportClass(): string
    {
        return RoleExport::class;
    }

    /**
     * Get export data.
     *
     * @return array
     */
    protected function getExportData(): array
    {
        return Role::query()
            ->with("permissions")
            ->select("id", "name", "guard_name", "created_at", "updated_at")
            ->get()
            ->map(function ($role) {
                $permissionNames = $role->permissions->pluck("name")->toArray();

                return [
                    "id" => $role->id,
                    "name" => $role->name,
                    "guard_name" => $role->guard_name,
                    "permissions" => implode(", ", $permissionNames),
                    "created_at" => \Carbon\Carbon::parse($role->created_at)->format("Y-m-d H:i:s"),
                    "updated_at" => \Carbon\Carbon::parse($role->updated_at)->format("Y-m-d H:i:s"),
                ];
            })
            ->toArray();
    }

    /**
     * Get file name for export.
     *
     * @return string
     */
    protected function getFileName(): string
    {
        return "roles-export-" . now()->format("Y-m-d-His");
    }
}
