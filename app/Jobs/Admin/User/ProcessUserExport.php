<?php

namespace App\Jobs\Admin\User;

use App\Jobs\Base\ProcessExportJob;
use App\Exports\Admin\User\UserExport;
use App\Models\User;

class ProcessUserExport extends ProcessExportJob
{
    /**
     * Get the export class name.
     *
     * @return string
     */
    protected function getExportClass(): string
    {
        return UserExport::class;
    }

    /**
     * Get export data.
     *
     * @return array
     */
    protected function getExportData(): array
    {
        return User::query()
            ->with("roles")
            ->select("id", "name", "email", "email_verified_at", "created_at", "updated_at")
            ->get()
            ->map(function ($user) {
                $roleNames = $user->roles->pluck("name")->toArray();

                return [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "roles" => implode(", ", $roleNames),
                    "email_verified_at" => \Carbon\Carbon::parse($user->email_verified_at)->format("Y-m-d H:i:s"),
                    "created_at" => \Carbon\Carbon::parse($user->created_at)->format("Y-m-d H:i:s"),
                    "updated_at" => \Carbon\Carbon::parse($user->updated_at)->format("Y-m-d H:i:s"),
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
        return "users-export-" . now()->format("Y-m-d-His");
    }
}
