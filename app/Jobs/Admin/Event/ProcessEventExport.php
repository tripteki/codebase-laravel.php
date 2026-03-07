<?php

namespace App\Jobs\Admin\Event;

use App\Enum\Event\AddOnEnum;
use App\Jobs\Base\ProcessExportJob;
use App\Exports\Admin\Event\EventExport;
use App\Models\Tenant;

class ProcessEventExport extends ProcessExportJob
{
    /**
     * @return string
     */
    protected function getExportClass(): string
    {
        return EventExport::class;
    }

    /**
     * @return array
     */
    protected function getExportData(): array
    {
        return Tenant::query()
            ->with("domains")
            ->orderBy("created_at", "desc")
            ->get()
            ->map(function (Tenant $tenant) {
                $startDate = (string) ($tenant->getAttribute("event_start_date") ?? "");
                $endDate = (string) ($tenant->getAttribute("event_end_date") ?? "");
                $startTime = (string) ($tenant->getAttribute("event_start_time") ?? "");
                $endTime = (string) ($tenant->getAttribute("event_end_time") ?? "");

                $addOnsFeatures = $this->tenantAddOnsFeatures($tenant);
                $addOnsModules = $this->tenantAddOnsModules($tenant);

                $keyVisual = $tenant->getAttribute("key_visual");
                $primaryColor = is_array($keyVisual) && isset($keyVisual["primary_color"])
                    ? (string) $keyVisual["primary_color"]
                    : (string) ($tenant->getAttribute("primary_color") ?? "");
                $secondaryColor = is_array($keyVisual) && isset($keyVisual["secondary_color"])
                    ? (string) $keyVisual["secondary_color"]
                    : (string) ($tenant->getAttribute("secondary_color") ?? "");
                $tertiaryColor = is_array($keyVisual) && isset($keyVisual["tertiary_color"])
                    ? (string) $keyVisual["tertiary_color"]
                    : (string) ($tenant->getAttribute("tertiary_color") ?? "");

                $row = [
                    "slug" => (string) $tenant->id,
                    "title" => (string) ($tenant->getAttribute("title") ?? ""),
                ];
                if (config("tenancy.type") !== "path") {
                    $centralDomains = config("tenancy.central_domains", []);
                    $centralHost = is_array($centralDomains) && isset($centralDomains[0]) ? $centralDomains[0] : "";
                    $row["domains"] = $tenant->domains
                        ->map(fn ($d) => tenant_public_domain_url((string) $d->domain, $centralHost))
                        ->filter(fn (string $u) => $u !== "")
                        ->implode(", ");
                }
                return array_merge($row, [
                    "key_visual_primary_color" => $primaryColor,
                    "key_visual_secondary_color" => $secondaryColor,
                    "key_visual_tertiary_color" => $tertiaryColor,
                    "event_start_date" => $startDate,
                    "event_start_time" => $startTime,
                    "event_end_date" => $endDate,
                    "event_end_time" => $endTime,
                    "add_ons_features" => $addOnsFeatures,
                    "add_ons_modules" => $addOnsModules,
                    "updated_at" => $tenant->updated_at ? $tenant->updated_at->format("Y-m-d H:i:s") : "",
                ]);
            })
            ->toArray();
    }

    /**
     * @param \App\Models\Tenant $tenant
     * @return string
     */
    protected function tenantAddOnsFeatures(Tenant $tenant): string
    {
        $raw = $tenant->getAttribute("add_ons_features");
        if (is_array($raw) && $raw !== []) {
            return implode(",", array_map("trim", $raw));
        }
        if (is_string($raw) && trim($raw) !== "") {
            return $raw;
        }

        return implode(",", array_map(fn (AddOnEnum $c) => $c->value, AddOnEnum::features()));
    }

    /**
     * @param \App\Models\Tenant $tenant
     * @return string
     */
    protected function tenantAddOnsModules(Tenant $tenant): string
    {
        $raw = $tenant->getAttribute("add_ons_modules");
        if (is_array($raw) && $raw !== []) {
            return implode(",", array_map("trim", $raw));
        }
        if (is_string($raw) && trim($raw) !== "") {
            return $raw;
        }

        $modules = array_filter(AddOnEnum::cases(), fn (AddOnEnum $c) => $c->isModule());

        return implode(",", array_map(fn (AddOnEnum $c) => $c->value, $modules));
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return "events-export-" . now()->format("Y-m-d-His");
    }
}
