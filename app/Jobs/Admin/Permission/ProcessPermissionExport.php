<?php

namespace App\Jobs\Admin\Permission;

use App\Jobs\Base\ProcessExportJob;
use App\Exports\Admin\Permission\PermissionExport;
use Src\V1\Api\Acl\Models\Permission;

class ProcessPermissionExport extends ProcessExportJob
{
    /**
     * @return string
     */
    protected function getExportClass(): string
    {
        return PermissionExport::class;
    }

    /**
     * @return array
     */
    protected function getExportData(): array
    {
        return Permission::query()
            ->select("id", "name", "guard_name", "created_at", "updated_at")
            ->get()
            ->map(function ($permission) {
                return [
                    "id" => $permission->id,
                    "name" => $permission->name,
                    "guard_name" => $permission->guard_name,
                    "created_at" => \Carbon\Carbon::parse($permission->created_at)->format("Y-m-d H:i:s"),
                    "updated_at" => \Carbon\Carbon::parse($permission->updated_at)->format("Y-m-d H:i:s"),
                ];
            })
            ->toArray();
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return "permissions-export-" . now()->format("Y-m-d-His");
    }
}
