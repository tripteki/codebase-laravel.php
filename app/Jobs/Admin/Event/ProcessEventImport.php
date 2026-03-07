<?php

namespace App\Jobs\Admin\Event;

use App\Enum\Event\AddOnEnum;
use App\Jobs\Base\ProcessImportJob;
use App\Imports\Admin\Event\EventImport;
use App\Models\Tenant;
use App\Models\User;
use App\Helpers\Tenant\TenantTranslationContent;
use Database\Seeders\TenantTranslationContentDefaults;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\User\Enums\UserEnum;
use Src\V1\Api\User\Database\Seeders\CreateUserSeeder;

class ProcessEventImport extends ProcessImportJob
{
    /**
     * @return string
     */
    protected function getImportClass(): string
    {
        return EventImport::class;
    }

    /**
     * @param array $normalizedData
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidator(array $normalizedData): Validator
    {
        $slug = trim((string) ($normalizedData["slug"] ?? ""));
        $domainsRaw = (string) ($normalizedData["domains"] ?? $normalizedData["domain"] ?? "");
        $normalizedData["domains"] = $domainsRaw;
        $domainList = config("tenancy.type") === "path"
            ? ($slug !== "" ? [$slug] : [])
            : array_filter(array_map("trim", explode(",", $domainsRaw)));
        if ($slug !== "") {
            $normalizedData["slug"] = $slug;
        }
        $normalizedData["add_ons_features"] = $this->normalizeAddOnsColumn($normalizedData, "add_ons_features", "features");
        $normalizedData["add_ons_modules"] = $this->normalizeAddOnsColumn($normalizedData, "add_ons_modules", "modules");

        $normalizedData["key_visual_primary_color"] = $this->normalizeKeyVisualColor($normalizedData, "primary");
        $normalizedData["key_visual_secondary_color"] = $this->normalizeKeyVisualColor($normalizedData, "secondary");
        $normalizedData["key_visual_tertiary_color"] = $this->normalizeKeyVisualColor($normalizedData, "tertiary");

        $domainRequired = config("tenancy.type") !== "path";
        $validator = ValidatorFacade::make($normalizedData, [
            "slug" => "required|string|max:255|regex:/^[a-z0-9\\-]+$/",
            "title" => "nullable|string|max:2000",
            "domains" => $domainRequired ? "required|string|max:500" : "nullable|string|max:500",
            "key_visual_primary_color" => ["nullable", "string", "max:20", "regex:/^(#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})?$/"],
            "key_visual_secondary_color" => ["nullable", "string", "max:20", "regex:/^(#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})?$/"],
            "key_visual_tertiary_color" => ["nullable", "string", "max:20", "regex:/^(#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})?$/"],
            "event_start_date" => "nullable|date",
            "event_end_date" => "nullable|date",
            "event_start_time" => "nullable|date_format:H:i",
            "event_end_time" => "nullable|date_format:H:i",
            "add_ons_features" => "nullable|string|max:500",
            "add_ons_modules" => "nullable|string|max:500",
        ], [
            "slug.required" => __("validation.required", ["attribute" => __("module_event.slug")]),
            "domains.required" => __("validation.required", ["attribute" => __("module_event.domains")]),
            "slug.regex" => __("module_event.slug_format"),
            "key_visual_primary_color.regex" => __("module_event.primary_color_placeholder"),
            "key_visual_secondary_color.regex" => __("module_event.secondary_color_placeholder"),
            "key_visual_tertiary_color.regex" => __("module_event.tertiary_color_placeholder"),
        ]);

        $validator->after(function (Validator $v) use ($normalizedData, $domainList, $slug): void {
            if ($slug !== "" && Tenant::query()->where("id", $slug)->exists()) {
                $v->errors()->add("slug", __("validation.unique", ["attribute" => __("module_event.slug")]));
            }
            foreach ($domainList as $domain) {
                if ($domain === "") {
                    continue;
                }
                if (DB::table("domains")->where("domain", $domain)->exists()) {
                    $v->errors()->add("domains", __("validation.unique", ["attribute" => __("module_event.domain")]) . ": " . $domain);
                    break;
                }
            }
            $this->validateAddOnsValues($v, $normalizedData["add_ons_features"] ?? "", "add_ons_features", true);
            $this->validateAddOnsValues($v, $normalizedData["add_ons_modules"] ?? "", "add_ons_modules", false);
        });

        return $validator;
    }

    /**
     * @param array $row
     * @param string $which primary|secondary|tertiary
     * @return string
     */
    protected function normalizeKeyVisualColor(array $row, string $which): string
    {
        $key = "key_visual_{$which}_color";
        $alt = "{$which}_color";
        $v = $row[$key] ?? $row[$alt] ?? "";
        if (is_array($v)) {
            return implode("", $v);
        }

        return trim((string) $v);
    }

    /**
     * @param array $row
     * @param string $primaryKey
     * @param string $altKey
     * @return string
     */
    protected function normalizeAddOnsColumn(array $row, string $primaryKey, string $altKey): string
    {
        $v = $row[$primaryKey] ?? $row[$altKey] ?? "";
        if (is_array($v)) {
            return implode(",", $v);
        }

        return trim((string) $v);
    }

    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @param string $value
     * @param string $attribute
     * @param bool $featuresOnly
     * @return void
     */
    protected function validateAddOnsValues(Validator $validator, string $value, string $attribute, bool $featuresOnly): void
    {
        if ($value === "") {
            return;
        }
        $validValues = array_map(fn (AddOnEnum $c) => $c->value, $featuresOnly ? AddOnEnum::features() : array_filter(AddOnEnum::cases(), fn (AddOnEnum $c) => $c->isModule()));
        $parts = array_filter(array_map("trim", explode(",", $value)));
        foreach ($parts as $part) {
            if ($part !== "" && ! in_array($part, $validValues, true)) {
                $validator->errors()->add($attribute, __("module_event.add_on_invalid_value") . ": " . $part);
                break;
            }
        }
    }

    /**
     * @param array $validatedData
     * @param array $normalizedData
     * @return void
     */
    protected function processRow(array $validatedData, array $normalizedData): void
    {
        $slugValue = trim((string) ($validatedData["slug"] ?? ""));
        $domainList = config("tenancy.type") === "path"
            ? ($slugValue !== "" ? [$slugValue] : [])
            : array_filter(array_map("trim", explode(",", (string) ($validatedData["domains"] ?? ""))));
        if (empty($domainList)) {
            return;
        }
        $primaryColor = trim((string) ($validatedData["key_visual_primary_color"] ?? ""));
        $secondaryColor = trim((string) ($validatedData["key_visual_secondary_color"] ?? ""));
        $tertiaryColor = trim((string) ($validatedData["key_visual_tertiary_color"] ?? ""));

        $tenantData = [
            "title" => trim((string) ($validatedData["title"] ?? "")) ?: null,
            "key_visual" => [
                "primary_color" => $primaryColor !== "" ? $primaryColor : null,
                "secondary_color" => $secondaryColor !== "" ? $secondaryColor : null,
                "tertiary_color" => $tertiaryColor !== "" ? $tertiaryColor : null,
            ],
            "event_start_date" => $validatedData["event_start_date"] ?? null,
            "event_end_date" => $validatedData["event_end_date"] ?? null,
            "event_start_time" => $validatedData["event_start_time"] ?? null,
            "event_end_time" => $validatedData["event_end_time"] ?? null,
        ];
        $featuresStr = trim((string) ($validatedData["add_ons_features"] ?? ""));
        $modulesStr = trim((string) ($validatedData["add_ons_modules"] ?? ""));
        $tenantData["add_ons_features"] = $featuresStr !== ""
            ? array_values(array_filter(array_map("trim", explode(",", $featuresStr))))
            : [];
        $tenantData["add_ons_modules"] = $modulesStr !== ""
            ? array_values(array_filter(array_map("trim", explode(",", $modulesStr))))
            : [];

        $tenantData["id"] = $slugValue;

        $tenant = Tenant::create($tenantData);
        foreach ($domainList as $domain) {
            if ($domain !== "") {
                $tenant->domains()->create(["domain" => $domain]);
            }
        }

        TenantTranslationContent::ensureForBackedEnums(
            (string) $tenant->id,
            TenantTranslationContentDefaults::backedEnums(),
        );

        $adminUser = User::query()->create([
            "name" => UserEnum::ADMIN->value,
            "email" => UserEnum::ADMIN->value . "." . $tenant->id . "@" . config("app.email_server"),
            "password" => CreateUserSeeder::DEFAULT_PASSWORD,
            "tenant_id" => $tenant->id,
        ]);

        $adminUser->markEmailAsVerified();

        $adminUser->assignRole(RoleEnum::ADMIN->value);
    }
}
