<?php

namespace App\Jobs\Admin\Stage;

use App\Exports\Admin\Stage\SessionExport;
use App\Jobs\Base\ProcessExportJob;
use App\Models\StageSession;

class ProcessSessionExport extends ProcessExportJob
{
    /**
     * @return string
     */
    protected function getExportClass(): string
    {
        return SessionExport::class;
    }

    /**
     * @return array
     */
    protected function getExportData(): array
    {
        $q = StageSession::query()
            ->with(["speakers.user"])
            ->select("id", "room_id", "title", "description", "start_at", "end_at")
            ->orderBy("created_at", "desc");

        if (config("tenancy.is_tenancy")) {
            $q->with("tenant.domains")->addSelect("tenant_id");
        }

        return $q->get()
            ->map(function (StageSession $session) {
                $speakerEmails = $session->speakers
                    ->map(fn ($inv) => $inv->user?->email)
                    ->filter()
                    ->unique()
                    ->values()
                    ->implode(",");

                $row = [
                    $session->room_id ?? "",
                ];

                if (config("tenancy.is_tenancy")) {
                    $tenantDisplay = null;
                    if ($session->tenant_id && $session->tenant) {
                        $tenant = $session->tenant;
                        if ($tenant && $tenant->domains) {
                            $domain = $tenant->domains->first();
                            $tenantDisplay = $domain ? $domain->domain : $session->tenant_id;
                        } else {
                            $tenantDisplay = $session->tenant_id;
                        }
                    }

                    $row[] = $tenantDisplay ?? "";
                }

                $row[] = $session->title;
                $row[] = $session->description ?? "";
                $row[] = $session->start_at?->format("Y-m-d H:i:s") ?? "";
                $row[] = $session->end_at?->format("Y-m-d H:i:s") ?? "";
                $row[] = $speakerEmails;

                return $row;
            })
            ->toArray();
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return "stage-sessions-export-" . now()->format("Y-m-d-His");
    }
}
