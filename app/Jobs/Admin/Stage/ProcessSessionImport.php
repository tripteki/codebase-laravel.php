<?php

namespace App\Jobs\Admin\Stage;

use App\Imports\Admin\Stage\SessionImport;
use App\Jobs\Base\ProcessImportJob;
use App\Enum\Stage\StageSessionStatusEnum;
use App\Models\StageInvitation;
use App\Models\StageSession;
use App\Models\User;
use App\Notifications\StageSessionSpeakerNotification;
use Illuminate\Support\Facades\Validator;
use Stancl\Tenancy\Database\Models\Domain;

class ProcessSessionImport extends ProcessImportJob
{
    /**
     * @return string
     */
    protected function getImportClass(): string
    {
        return SessionImport::class;
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
            "speakers" => ["nullable", "string", "max:1000"],
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
        $session = new StageSession();
        $session->title = $validatedData["title"];
        $session->description = ! empty($validatedData["description"] ?? null) ? $validatedData["description"] : null;
        $session->start_at = ! empty($validatedData["start_at"] ?? null) ? \Carbon\Carbon::parse($validatedData["start_at"]) : null;
        $session->end_at = ! empty($validatedData["end_at"] ?? null) ? \Carbon\Carbon::parse($validatedData["end_at"]) : null;

        if (config("tenancy.is_tenancy") && isset($validatedData["tenant_id"]) && filled($validatedData["tenant_id"])) {
            $session->tenant_id = $validatedData["tenant_id"];
        }

        $session->save();

        $speakerEmailsRaw = $normalizedData["speakers"] ?? "";
        $speakerEmails = array_unique(array_filter(array_map("trim", explode(",", (string) $speakerEmailsRaw))));

        if (! empty($speakerEmails)) {
            $speakerUsers = User::query()
                ->whereIn("email", $speakerEmails)
                ->orderBy("id")
                ->get();

            foreach ($speakerUsers as $speakerUser) {
                $inv = StageInvitation::firstOrCreate(
                    [
                        "invitationable_type" => StageSession::class,
                        "invitationable_id" => $session->id,
                        "role" => StageInvitation::ROLE_SPEAKER,
                        "user_id" => $speakerUser->id,
                    ],
                    [
                        "tenant_id" => config("tenancy.is_tenancy") ? $session->tenant_id : null,
                        "staged" => StageSessionStatusEnum::defaultStaged(),
                    ]
                );
                $inv->fill(["tenant_id" => config("tenancy.is_tenancy") ? $session->tenant_id : null]);
                $inv->save();
                $speakerUser->notify(new StageSessionSpeakerNotification($session, url(tenant_routes("admin.stage.sessions.show", $session))));
            }
        }
    }
}
