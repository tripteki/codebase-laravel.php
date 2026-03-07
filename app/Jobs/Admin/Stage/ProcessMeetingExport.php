<?php

namespace App\Jobs\Admin\Stage;

use App\Exports\Admin\Stage\MeetingExport;
use App\Jobs\Base\ProcessExportJob;
use App\Models\StageMeeting;
use App\Models\StageInvitation;

class ProcessMeetingExport extends ProcessExportJob
{
    /**
     * @return string
     */
    protected function getExportClass(): string
    {
        return MeetingExport::class;
    }

    /**
     * @return array
     */
    protected function getExportData(): array
    {
        $q = StageMeeting::query()
            ->with(["invitation.user", "exhibitorSponsors"])
            ->select("id", "room_id", "title", "description", "start_at", "end_at")
            ->orderBy("created_at", "desc");

        if (config("tenancy.is_tenancy")) {
            $q->with("tenant.domains")->addSelect("tenant_id");
        }

        return $q->get()
            ->map(function (StageMeeting $meeting) {
                $delegateEmail = $meeting->invitation?->user?->email ?? "";

                $exhibitorEmails = $meeting->exhibitorSponsors
                    ->pluck("email")
                    ->filter()
                    ->implode(",");

                $row = [
                    $meeting->room_id ?? "",
                ];

                if (config("tenancy.is_tenancy")) {
                    $tenantDisplay = null;
                    if ($meeting->tenant_id && $meeting->tenant) {
                        $tenant = $meeting->tenant;
                        if ($tenant && $tenant->domains) {
                            $domain = $tenant->domains->first();
                            $tenantDisplay = $domain ? $domain->domain : $meeting->tenant_id;
                        } else {
                            $tenantDisplay = $meeting->tenant_id;
                        }
                    }

                    $row[] = $tenantDisplay ?? "";
                }

                $row[] = $meeting->title;
                $row[] = $meeting->description ?? "";
                $row[] = $meeting->start_at?->format("Y-m-d H:i:s") ?? "";
                $row[] = $meeting->end_at?->format("Y-m-d H:i:s") ?? "";
                $row[] = $delegateEmail;
                $row[] = $exhibitorEmails;

                return $row;
            })
            ->toArray();
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return "stage-meetings-export-" . now()->format("Y-m-d-His");
    }
}
