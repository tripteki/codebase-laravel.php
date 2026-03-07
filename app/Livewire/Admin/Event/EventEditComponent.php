<?php

namespace App\Livewire\Admin\Event;

use App\Helpers\SettingHelper;
use App\Enum\Event\AddOnEnum;
use App\Models\Tenant;
use App\Enum\Tenant\PermissionEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class EventEditComponent extends Component
{
    use WithFileUploads;

    /**
     * @var \App\Models\Tenant
     */
    public Tenant $tenant;

    /**
     * @var string
     */
    public $slug = "";

    /**
     * @var string
     */
    public $title = "";

    /**
     * @var string
     */
    public $description = "";

    /**
     * @var string|null
     */
    public $eventStartDate = null;

    /**
     * @var string|null
     */
    public $eventEndDate = null;

    /**
     * @var string|null
     */
    public $eventStartTime = null;

    /**
     * @var string|null
     */
    public $eventEndTime = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $icon = null;

    /**
     * @var string|null
     */
    public $iconUrl = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $faviconIco = null;

    /**
     * @var string|null
     */
    public $faviconIcoUrl = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $faviconPng = null;

    /**
     * @var string|null
     */
    public $faviconPngUrl = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $brandLight = null;

    /**
     * @var string|null
     */
    public $brandLightUrl = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $brandDark = null;

    /**
     * @var string|null
     */
    public $brandDarkUrl = null;

    /**
     * @var string|null
     */
    public $primaryColor = null;

    /**
     * @var string|null
     */
    public $secondaryColor = null;

    /**
     * @var string|null
     */
    public $tertiaryColor = null;

    /**
     * @var array<int, array{id: int|null, domain: string}>
     */
    public $domains = [];

    /**
     * @var int
     */
    public int $step = 1;

    /**
     * @var string
     */
    public string $pageRoute = '';

    /**
     * @var array<int, string>
     */
    public array $addOnsFeatures = [];

    /**
     * @var array<int, string>
     */
    public array $addOnsModules = [];

    /**
     * @var array<string, array<int, array{key: string, value: string}>>
     */
    public array $featureConfigRows = [];

    /**
     * @param \App\Models\Tenant $tenant
     * @return void
     */
    public function mount(Tenant $tenant): void
    {
        $this->authorize(PermissionEnum::EVENT_UPDATE->value);

        $this->pageRoute = request()->route()->getName();
        $this->tenant = $tenant->load("domains");
        $this->slug = (string) $tenant->id;
        $this->title = (string) ($tenant->getAttribute("title") ?? "");
        $this->description = (string) ($tenant->getAttribute("description") ?? "");
        $this->eventStartDate = (string) ($tenant->getAttribute("event_start_date") ?? "");
        $this->eventEndDate = (string) ($tenant->getAttribute("event_end_date") ?? "");
        $this->eventStartTime = (string) ($tenant->getAttribute("event_start_time") ?? "");
        $this->eventEndTime = (string) ($tenant->getAttribute("event_end_time") ?? "");
        $this->iconUrl = $tenant->getAttribute("icon") ? (string) $tenant->getAttribute("icon") : null;
        $this->faviconIcoUrl = $tenant->getAttribute("favicon_ico") ? (string) $tenant->getAttribute("favicon_ico") : null;
        $this->faviconPngUrl = $tenant->getAttribute("favicon_png") ? (string) $tenant->getAttribute("favicon_png") : null;
        $this->brandLightUrl = $tenant->getAttribute("brand_light") ? (string) $tenant->getAttribute("brand_light") : null;
        $this->brandDarkUrl = $tenant->getAttribute("brand_dark") ? (string) $tenant->getAttribute("brand_dark") : null;
        $p = SettingHelper::get("COLOR_PRIMARY");
        $s = SettingHelper::get("COLOR_SECONDARY");
        $t = SettingHelper::get("COLOR_TERTIARY");
        $this->primaryColor = $tenant->getAttribute("primary_color") ?? ($p !== null ? trim((string) $p) : null);
        $this->secondaryColor = $tenant->getAttribute("secondary_color") ?? ($s !== null ? trim((string) $s) : null);
        $this->tertiaryColor = $tenant->getAttribute("tertiary_color") ?? ($t !== null ? trim((string) $t) : null);
        $this->domains = $tenant->domains->map(fn ($d) => ["id" => $d->id, "domain" => $d->domain])->toArray();
        if (empty($this->domains)) {
            $this->domains = [["id" => null, "domain" => ""]];
        }

        $featuresRaw = $tenant->getAttribute("add_ons_features");
        $this->addOnsFeatures = $this->parseAddOnsToArray($featuresRaw);
        $modulesRaw = $tenant->getAttribute("add_ons_modules");
        $this->addOnsModules = $this->parseAddOnsToArray($modulesRaw);

        $addOnsConfig = $tenant->getAttribute("add_ons_config");
        if (is_array($addOnsConfig)) {
            foreach ($addOnsConfig as $featureValue => $assoc) {
                if (! is_array($assoc)) {
                    continue;
                }
                $rows = [];
                foreach ($assoc as $k => $v) {
                    $val = $k === "password" ? "" : (string) $v;
                    $rows[] = ["key" => (string) $k, "value" => $val];
                }
                $this->featureConfigRows[$featureValue] = $rows;
            }
        }
    }

    /**
     * @param string $featureValue
     * @return array<int, array{key: string, value: string}>
     */
    public function getFeatureConfigRows(string $featureValue): array
    {
        $rows = $this->featureConfigRows[$featureValue] ?? [];
        return is_array($rows) ? $rows : [];
    }

    /**
     * @param string $featureValue
     * @return void
     */
    public function addFeatureConfigRow(string $featureValue): void
    {
        if (! isset($this->featureConfigRows[$featureValue])) {
            $this->featureConfigRows[$featureValue] = [];
        }
        $this->featureConfigRows[$featureValue][] = ["key" => "", "value" => ""];
    }

    /**
     * @param string $featureValue
     * @param int $index
     * @return void
     */
    public function removeFeatureConfigRow(string $featureValue, int $index): void
    {
        $rows = $this->getFeatureConfigRows($featureValue);
        if ($index < 0 || $index >= count($rows)) {
            return;
        }
        array_splice($rows, $index, 1);
        $this->featureConfigRows[$featureValue] = $rows;
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    protected function buildAddOnsConfig(): array
    {
        $config = [];
        foreach (AddOnEnum::features() as $feature) {
            $value = $feature->value;
            if (! in_array($value, $this->addOnsFeatures, true)) {
                continue;
            }

            $rows = $this->getFeatureConfigRows($value);

            $rawAssoc = [];
            foreach ($rows as $row) {
                $k = trim((string) ($row["key"] ?? ""));
                if ($k === "") {
                    continue;
                }
                $v = $row["value"] ?? "";
                $rawAssoc[$k] = $v;
            }

            if ($rawAssoc === []) {
                continue;
            }

            $assoc = [];
            if ($feature === AddOnEnum::FEATURES_MAILING) {
                foreach ($rawAssoc as $k => $v) {
                    $v = (string) $v;
                    switch ($k) {
                        case "MAIL_HOST":
                            $assoc["host"] = $v;
                            break;
                        case "MAIL_PORT":
                            $assoc["port"] = $v;
                            break;
                        case "MAIL_USERNAME":
                            $assoc["username"] = $v;
                            break;
                        case "MAIL_PASSWORD":
                            $assoc["password"] = $v;
                            break;
                        case "MAIL_FROM_ADDRESS":
                            $assoc["from_address"] = $v;
                            break;
                        case "MAIL_FROM_NAME":
                            $assoc["from_name"] = $v;
                            break;
                        case "MAIL_ENCRYPTION":
                            $assoc["encryption"] = $v;
                            break;
                        default:
                            $assoc[$k] = $v;
                            break;
                    }
                }
            } elseif ($feature === AddOnEnum::FEATURES_COPYRIGHT) {
                foreach ($rawAssoc as $k => $v) {
                    $v = (string) $v;
                    if ($k === "OWNER" || $k === "owner") {
                        $assoc["owner"] = $v;
                    } else {
                        $assoc[$k] = $v;
                    }
                }
            } else {
                $assoc = array_map(static fn ($v) => (string) $v, $rawAssoc);
            }

            if (isset($assoc["password"]) && $assoc["password"] !== "") {
                $assoc["password"] = \Illuminate\Support\Facades\Crypt::encryptString($assoc["password"]);
            }

            $finalAssoc = [];
            foreach ($assoc as $k => $v) {
                $v = (string) $v;
                $finalAssoc[$k] = $v !== "" ? $v : null;
            }

            if ($finalAssoc !== []) {
                $config[$value] = $finalAssoc;
            }
        }

        return $config;
    }

    /**
     * @param mixed $raw Array or comma-separated string (backward compat).
     * @return array<int, string>
     */
    protected function parseAddOnsToArray($raw): array
    {
        if (is_array($raw)) {
            return array_values(array_filter(array_map("trim", $raw)));
        }
        if (! is_string($raw) || trim($raw) === "") {
            return [];
        }

        return array_values(array_filter(array_map("trim", explode(",", $raw))));
    }

    /**
     * @param string $value
     * @return void
     */
    public function toggleAddOnFeature(string $value): void
    {
        $key = array_search($value, $this->addOnsFeatures, true);
        if ($key !== false) {
            unset($this->addOnsFeatures[$key]);
            $this->addOnsFeatures = array_values($this->addOnsFeatures);
        } else {
            $this->addOnsFeatures[] = $value;
        }
    }

    /**
     * @param string $value
     * @return void
     */
    public function toggleAddOnModule(string $value): void
    {
        $key = array_search($value, $this->addOnsModules, true);
        if ($key !== false) {
            unset($this->addOnsModules[$key]);
            $this->addOnsModules = array_values($this->addOnsModules);
        } else {
            $this->addOnsModules[] = $value;
        }
    }

    /**
     * @param string $value
     * @return bool
     */
    public function hasAddOnFeature(string $value): bool
    {
        return in_array($value, $this->addOnsFeatures, true);
    }

    /**
     * @param string $value
     * @return bool
     */
    public function hasAddOnModule(string $value): bool
    {
        return in_array($value, $this->addOnsModules, true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            "slug" => "required|string|max:255|regex:/^[a-z0-9\-]+$/",
            "title" => "required|string|max:2000",
            "description" => "nullable|string|max:5000",
            "eventStartDate" => "nullable|date",
            "eventEndDate" => "nullable|date",
            "eventStartTime" => "nullable|date_format:H:i",
            "eventEndTime" => "nullable|date_format:H:i",
            "icon" => "nullable|image|max:2048",
            "faviconIco" => "nullable|file|max:2048",
            "faviconPng" => "nullable|image|max:2048",
            "brandLight" => "nullable|image|max:4096",
            "brandDark" => "nullable|image|max:4096",
            "domains" => "required|array|min:1",
            "domains.*.domain" => "required|string|max:255",
            "primaryColor" => "nullable|string|max:20",
            "secondaryColor" => "nullable|string|max:20",
            "tertiaryColor" => "nullable|string|max:20",
        ];
    }

    /**
     * @return void
     */
    public function addDomain(): void
    {
        $this->domains[] = ["id" => null, "domain" => ""];
    }

    /**
     * @param int $index
     * @return void
     */
    public function removeDomain(int $index): void
    {
        if (count($this->domains) <= 1) {
            return;
        }
        array_splice($this->domains, $index, 1);
    }

    /**
     * @return void
     */
    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                "slug" => "required|string|max:255|regex:/^[a-z0-9\\-]+$/",
                "title" => "required|string|max:2000",
                "description" => "nullable|string|max:5000",
            ], [
                "slug.required" => __("validation.required", ["attribute" => __("module_event.slug")]),
                "slug.regex" => __("module_event.slug_format"),
                "title.required" => __("validation.required", ["attribute" => __("module_event.title")]),
            ]);

            $slug = trim($this->slug);
            if ($slug !== (string) $this->tenant->id) {
                $this->addError("slug", __("validation.custom.slug.immutable") ?? "Slug cannot be changed after creation.");
                return;
            }
            if (config("tenancy.type") === "path") {
                $this->step = 3;
                return;
            }
        }
        if ($this->step === 2) {
            $rules = [
                "domains" => "required|array|min:1",
                "domains.*.domain" => "required|string|max:255",
            ];
            foreach ($this->domains as $i => $d) {
                $rules["domains.{$i}.domain"] = [
                    "required",
                    "string",
                    "max:255",
                    Rule::unique("domains", "domain")->ignore($d["id"] ?? 0),
                ];
            }
            $this->validate($rules, [
                "domains.required" => __("module_event.domain_required"),
                "domains.*.domain.unique" => __("validation.unique", ["attribute" => __("module_event.domain")]),
            ]);
            $domainStrings = array_values(array_filter(array_map(fn ($d) => trim((string) ($d["domain"] ?? "")), $this->domains)));
            if (empty($domainStrings)) {
                $this->addError("domains.0.domain", __("module_event.domain_required"));
                return;
            }
        }
        if ($this->step < 4) {
            $this->step++;
        }
    }

    /**
     * @return void
     */
    public function previousStep(): void
    {
        if ($this->step > 1) {
            if (config("tenancy.type") === "path" && $this->step === 3) {
                $this->step = 1;
            } else {
                $this->step--;
            }
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function save()
    {
        $this->authorize(PermissionEnum::EVENT_UPDATE->value);

        $rules = [
            "title" => "required|string|max:2000",
            "description" => "nullable|string|max:5000",
            "eventStartDate" => "nullable|date",
            "eventEndDate" => "nullable|date",
            "eventStartTime" => "nullable|date_format:H:i",
            "eventEndTime" => "nullable|date_format:H:i",
            "icon" => "nullable|image|max:2048",
            "faviconIco" => "nullable|file|max:2048",
            "faviconPng" => "nullable|image|max:2048",
            "brandLight" => "nullable|image|max:4096",
            "brandDark" => "nullable|image|max:4096",
            "primaryColor" => "nullable|string|max:20",
            "secondaryColor" => "nullable|string|max:20",
            "tertiaryColor" => "nullable|string|max:20",
        ];
        if (config("tenancy.type") !== "path") {
            $rules["domains"] = "required|array|min:1";
            $rules["domains.*.domain"] = "required|string|max:255";
            foreach ($this->domains as $i => $d) {
                $rules["domains.{$i}.domain"] = [
                    "required",
                    "string",
                    "max:255",
                    Rule::unique("domains", "domain")->ignore($d["id"] ?? 0),
                ];
            }
        }
        $this->validate($rules, [
            "title.required" => __("validation.required", ["attribute" => __("module_event.title")]),
            "icon.image" => __("module_event.icon_must_be_image"),
            "domains.required" => __("module_event.domain_required"),
            "domains.*.domain.unique" => __("validation.unique", ["attribute" => __("module_event.domain")]),
        ]);

        $domainStrings = array_values(array_filter(array_map(fn ($d) => trim((string) ($d["domain"] ?? "")), $this->domains)));
        if (empty($domainStrings)) {
            $this->addError("domains.0.domain", __("module_event.domain_required"));
            return;
        }

        $iconPath = $this->tenant->getAttribute("icon");
        if ($this->icon) {
            if ($iconPath) {
                Storage::disk("public")->delete($iconPath);
            }
            $iconPath = $this->icon->store("tenant-icons", "public");
            $this->iconUrl = $iconPath;
            $this->reset("icon");
        }

        $faviconIcoPath = $this->tenant->getAttribute("favicon_ico");

        if ($this->faviconIco) {
            if ($faviconIcoPath) {
                Storage::disk("public")->delete($faviconIcoPath);
            }

            $faviconIcoPath = $this->faviconIco->store("tenant-icons", "public");

            $this->faviconIcoUrl = $faviconIcoPath;
            $this->reset("faviconIco");
        }

        $faviconPngPath = $this->tenant->getAttribute("favicon_png");
        if ($this->faviconPng) {
            if ($faviconPngPath) {
                Storage::disk("public")->delete($faviconPngPath);
            }
            $faviconPngPath = $this->faviconPng->store("tenant-icons", "public");
            $this->faviconPngUrl = $faviconPngPath;
            $this->reset("faviconPng");
        }

        $brandLightPath = $this->tenant->getAttribute("brand_light");
        if ($this->brandLight) {
            if ($brandLightPath) {
                Storage::disk("public")->delete($brandLightPath);
            }
            $brandLightPath = $this->brandLight->store("tenant-icons", "public");
            $this->brandLightUrl = $brandLightPath;
            $this->reset("brandLight");
        }

        $brandDarkPath = $this->tenant->getAttribute("brand_dark");
        if ($this->brandDark) {
            if ($brandDarkPath) {
                Storage::disk("public")->delete($brandDarkPath);
            }
            $brandDarkPath = $this->brandDark->store("tenant-icons", "public");
            $this->brandDarkUrl = $brandDarkPath;
            $this->reset("brandDark");
        }

        DB::beginTransaction();
        try {
            $this->tenant->setAttribute("title", trim($this->title));
            $this->tenant->setAttribute("description", trim($this->description) ?: null);
            $this->tenant->setAttribute("event_start_date", $this->eventStartDate ?: null);
            $this->tenant->setAttribute("event_end_date", $this->eventEndDate ?: null);
            $this->tenant->setAttribute("event_start_time", $this->eventStartTime ?: null);
            $this->tenant->setAttribute("event_end_time", $this->eventEndTime ?: null);
            $this->tenant->setAttribute("icon", $iconPath);
            $this->tenant->setAttribute("favicon_ico", $faviconIcoPath);
            $this->tenant->setAttribute("favicon_png", $faviconPngPath);
            $this->tenant->setAttribute("brand_light", $brandLightPath);
            $this->tenant->setAttribute("brand_dark", $brandDarkPath);
            $this->tenant->setAttribute("primary_color", $this->primaryColor ?: null);
            $this->tenant->setAttribute("secondary_color", $this->secondaryColor ?: null);
            $this->tenant->setAttribute("tertiary_color", $this->tertiaryColor ?: null);
            $this->tenant->setAttribute("add_ons_features", $this->addOnsFeatures);
            $this->tenant->setAttribute("add_ons_modules", $this->addOnsModules);
            $this->tenant->setAttribute("add_ons_config", $this->buildAddOnsConfig());
            $this->tenant->save();

            if (config("tenancy.type") === "path") {
                $slugDomain = (string) $this->tenant->id;
                $this->tenant->domains()->delete();
                $this->tenant->domains()->create(["domain" => $slugDomain]);
            } else {
                $keepIds = collect($this->domains)->pluck("id")->filter()->values()->toArray();
                $this->tenant->domains()->whereNotIn("id", $keepIds)->delete();

                foreach ($this->domains as $d) {
                    $domain = trim((string) ($d["domain"] ?? ""));
                    if ($domain === "") {
                        continue;
                    }
                    if (! empty($d["id"])) {
                        $this->tenant->domains()->where("id", $d["id"])->update(["domain" => $domain]);
                    } else {
                        $this->tenant->domains()->create(["domain" => $domain]);
                    }
                }
            }
            DB::commit();
            session()->flash("message", __("module_event.updated_successfully"));
            return redirect()->to(tenant_routes("admin.tenants.events.show", $this->tenant));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash("error", $e->getMessage());
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.event.edit", [
            "tenant" => $this->tenant,
        ])->layout("layouts.app", [
            "title" => __("module_event.edit_event"),
            "showSidebar" => true,
        ]);
    }
}
