<?php

namespace App\Jobs\Admin\User;

use App\Jobs\Base\ProcessExportJob;
use App\Exports\Admin\User\UserExport;
use App\Models\User;

class ProcessUserExport extends ProcessExportJob
{
    /**
     * @return string
     */
    protected function getExportClass(): string
    {
        return UserExport::class;
    }

    /**
     * @return array
     */
    protected function getExportData(): array
    {
        $q = User::query()
            ->with("roles", "profile")
            ->select("id", "name", "email", "email_verified_at", "created_at", "updated_at");

        if (config("tenancy.is_tenancy")) {
            $q->with("tenant.domains")->addSelect("tenant_id");
        }

        return $q
            ->get()
            ->map(function ($user) {
                $roleNames = $user->roles->pluck("name")->toArray();

                $tenantDisplay = null;
                if (config("tenancy.is_tenancy") && $user->tenant_id) {
                    $tenant = $user->tenant;
                    if ($tenant && $tenant->domains) {
                        $domain = $tenant->domains->first();
                        $tenantDisplay = $domain ? $domain->domain : $user->tenant_id;
                    } else {
                        $tenantDisplay = $user->tenant_id;
                    }
                }

                $row = [
                    "id" => $user->id,
                    "full_name" => $user->profile?->full_name ?? "",
                    "name" => $user->name,
                    "email" => $user->email,
                    "roles" => implode(", ", $roleNames),
                ];
                if (config("tenancy.is_tenancy")) {
                    $row["tenant_id"] = $tenantDisplay ?? "";
                }
                $row["email_verified_at"] = $user->email_verified_at ? \Carbon\Carbon::parse($user->email_verified_at)->format("Y-m-d H:i:s") : "";
                $row["created_at"] = $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format("Y-m-d H:i:s") : "";
                $row["updated_at"] = $user->updated_at ? \Carbon\Carbon::parse($user->updated_at)->format("Y-m-d H:i:s") : "";

                return $row;
            })
            ->toArray();
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return "users-export-" . now()->format("Y-m-d-His");
    }
}
