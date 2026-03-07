<?php

namespace App\Jobs\Admin\Stage;

use App\Imports\Admin\Stage\MeetingImport;
use App\Jobs\Base\ProcessImportJob;
use App\Enum\Stage\StageMeetingStatusEnum;
use App\Models\StageMeeting;
use App\Models\StageInvitation;
use App\Models\User;
use App\Notifications\StageMeetingDelegateNotification;
use App\Notifications\StageMeetingExhibitorSponsorNotification;
use Illuminate\Support\Facades\Validator;
use Stancl\Tenancy\Database\Models\Domain;

class ProcessMeetingImport extends ProcessImportJob
{
    /**
     * @return string
     */
    protected function getImportClass(): string
    {
        return MeetingImport::class;
    }

    /**
     * @param array $rowData
     * @return array
     */
    protected function normalizeRowData(array $rowData): array
    {
        $normalizedData = parent::normalizeRowData($rowData);

        if (! config("tenancy.is_tenancy")) {
            unset($normalizedData["tenant_id"]);

            return $normalizedData;
        }

        $tenantIdValue = $normalizedData["tenant_id"] ?? null;

        if (filled($tenantIdValue) && str_contains((string) $tenantIdValue, ".")) {
            $domain = Domain::query()->where("domain", $tenantIdValue)->first();
            if ($domain) {
                $normalizedData["tenant_id"] = $domain->tenant_id;
            }
        }

        return $normalizedData;
    }

    /**
     * @param array $normalizedData
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidator(array $normalizedData): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            "title" => ["required", "string", "max:255"],
            "description" => ["nullable", "string", "max:5000"],
            "start_at" => ["nullable", "string", "date"],
            "end_at" => ["nullable", "string", "date", "after_or_equal:start_at"],
            "delegates" => ["nullable", "string", "max:1000"],
            "exhibitors_sponsors" => ["nullable", "string", "max:2000"],
        ];

        if (config("tenancy.is_tenancy")) {
            $rules["tenant_id"] = ["required", "string", "exists:tenants,id"];
        }

        return Validator::make($normalizedData, $rules);
    }

    /**
     * @param array $validatedData
     * @param array $normalizedData
     * @return void
     */
    protected function processRow(array $validatedData, array $normalizedData): void
    {
        $meeting = new StageMeeting();
        $meeting->title = $validatedData["title"];
        $meeting->description = ! empty($validatedData["description"] ?? null) ? $validatedData["description"] : null;
        $meeting->start_at = ! empty($validatedData["start_at"] ?? null) ? \Carbon\Carbon::parse($validatedData["start_at"]) : null;
        $meeting->end_at = ! empty($validatedData["end_at"] ?? null) ? \Carbon\Carbon::parse($validatedData["end_at"]) : null;

        if (config("tenancy.is_tenancy") && isset($validatedData["tenant_id"]) && filled($validatedData["tenant_id"])) {
            $meeting->tenant_id = $validatedData["tenant_id"];
        }

        $meeting->save();

        $delegateEmailsRaw = $normalizedData["delegates"] ?? "";
        $delegateEmails = array_filter(array_map("trim", explode(",", (string) $delegateEmailsRaw)));

        if (! empty($delegateEmails)) {
            $delegateUser = User::query()
                ->whereIn("email", $delegateEmails)
                ->orderBy("id")
                ->first();

            if ($delegateUser) {
                $inv = StageInvitation::firstOrCreate(
                    [
                        "invitationable_type" => StageMeeting::class,
                        "invitationable_id" => $meeting->id,
                        "role" => StageInvitation::ROLE_DELEGATE,
                    ],
                    [
                        "tenant_id" => config("tenancy.is_tenancy") ? $meeting->tenant_id : null,
                        "user_id" => $delegateUser->id,
                        "staged" => StageMeetingStatusEnum::defaultStaged(),
                    ]
                );
                $inv->fill([
                    "tenant_id" => config("tenancy.is_tenancy") ? $meeting->tenant_id : null,
                    "user_id" => $delegateUser->id,
                ]);
                $inv->save();
                $delegateUser->notify(new StageMeetingDelegateNotification($meeting, url(tenant_routes("admin.stage.meetings.show", $meeting))));
            }
        }

        $exhibitorsRaw = $normalizedData["exhibitors_sponsors"] ?? "";
        $exhibitorEmails = array_filter(array_map("trim", explode(",", (string) $exhibitorsRaw)));

        if (! empty($exhibitorEmails)) {
            $exhibitorUsers = User::query()
                ->whereIn("email", $exhibitorEmails)
                ->get();

            foreach ($exhibitorUsers as $user) {
                StageInvitation::create([
                    "tenant_id" => config("tenancy.is_tenancy") ? $meeting->tenant_id : null,
                    "invitationable_type" => StageMeeting::class,
                    "invitationable_id" => $meeting->id,
                    "role" => StageInvitation::ROLE_EXHIBITOR_SPONSOR,
                    "user_id" => $user->id,
                    "staged" => StageMeetingStatusEnum::defaultStaged(),
                ]);
                $user->notify(new StageMeetingExhibitorSponsorNotification($meeting, url(tenant_routes("admin.stage.meetings.show", $meeting))));
            }
        }
    }
}
