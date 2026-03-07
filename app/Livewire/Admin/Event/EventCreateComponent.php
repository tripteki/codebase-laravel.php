<?php

namespace App\Livewire\Admin\Event;

use App\Helpers\SettingHelper;
use App\Enum\Event\AddOnEnum;
use App\Models\Tenant;
use App\Models\User;
use App\Enum\Tenant\PermissionEnum;
use App\Helpers\Tenant\TenantTranslationContent;
use Database\Seeders\TenantTranslationContentDefaults;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\User\Enums\UserEnum;
use Src\V1\Api\User\Database\Seeders\CreateUserSeeder;

class EventCreateComponent extends Component
{
    use WithFileUploads;

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
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $faviconIco = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $faviconPng = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $brandLight = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $brandDark = null;

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
     * @var array<int, string>
     */
    public $domains = [""];

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
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::EVENT_CREATE->value);

        $this->pageRoute = request()->route()->getName();

        $this->primaryColor = ($p = SettingHelper::get('COLOR_PRIMARY')) !== null ? trim((string) $p) : null;
        $this->secondaryColor = ($s = SettingHelper::get('COLOR_SECONDARY')) !== null ? trim((string) $s) : null;
        $this->tertiaryColor = ($t = SettingHelper::get('COLOR_TERTIARY')) !== null ? trim((string) $t) : null;

        $this->addOnsFeatures = [AddOnEnum::FEATURES_EXPORT->value];
        $this->addOnsModules = [AddOnEnum::MODULES_ATTENDANCE_QRCODE->value];
    }

    /**
     * @param string $value AddOnEnum value (e.g. features_import).
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
     * @param string $value AddOnEnum value (e.g. modules_attendance_face_recognition).
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
        $this->featureConfigRows[$featureValue][] = ['key' => '', 'value' => ''];
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
                $k = trim((string) ($row['key'] ?? ''));
                if ($k === '') {
                    continue;
                }
                $v = $row['value'] ?? '';
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
                        case 'MAIL_HOST':
                            $assoc['host'] = $v;
                            break;
                        case 'MAIL_PORT':
                            $assoc['port'] = $v;
                            break;
                        case 'MAIL_USERNAME':
                            $assoc['username'] = $v;
                            break;
                        case 'MAIL_PASSWORD':
                            $assoc['password'] = $v;
                            break;
                        case 'MAIL_FROM_ADDRESS':
                            $assoc['from_address'] = $v;
                            break;
                        case 'MAIL_FROM_NAME':
                            $assoc['from_name'] = $v;
                            break;
                        case 'MAIL_ENCRYPTION':
                            $assoc['encryption'] = $v;
                            break;
                        default:
                            $assoc[$k] = $v;
                            break;
                    }
                }
            } elseif ($feature === AddOnEnum::FEATURES_COPYRIGHT) {
                foreach ($rawAssoc as $k => $v) {
                    $v = (string) $v;
                    if ($k === 'OWNER' || $k === 'owner') {
                        $assoc['owner'] = $v;
                    } else {
                        $assoc[$k] = $v;
                    }
                }
            } else {
                $assoc = array_map(static fn ($v) => (string) $v, $rawAssoc);
            }

            if (isset($assoc['password']) && $assoc['password'] !== '') {
                $assoc['password'] = \Illuminate\Support\Facades\Crypt::encryptString($assoc['password']);
            }

            $finalAssoc = [];
            foreach ($assoc as $k => $v) {
                $v = (string) $v;
                $finalAssoc[$k] = $v !== '' ? $v : null;
            }

            if ($finalAssoc !== []) {
                $config[$value] = $finalAssoc;
            }
        }

        return $config;
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
            ], [
                "slug.required" => __("validation.required", ["attribute" => __("module_event.slug")]),
                "slug.regex" => __("module_event.slug_format"),
                "title.required" => __("validation.required", ["attribute" => __("module_event.title")]),
            ]);
            $slug = trim($this->slug);
            if (Tenant::query()->where("id", $slug)->exists()) {
                $this->addError("slug", __("validation.unique", ["attribute" => __("module_event.slug")]));
                return;
            }
            if (config("tenancy.type") === "path") {
                $this->step = 3;
                return;
            }
        }
        if ($this->step === 2) {
            $this->validate([
                "domains" => "required|array|min:1",
                "domains.*" => "required|string|max:255|unique:domains,domain",
            ], [
                "domains.required" => __("module_event.domain_required"),
                "domains.*.unique" => __("validation.unique", ["attribute" => __("module_event.domain")]),
            ]);
            $domainStrings = array_values(array_filter(array_map("trim", $this->domains)));
            if (empty($domainStrings)) {
                $this->addError("domains.0", __("module_event.domain_required"));
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
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            "slug" => "required|string|max:255|regex:/^[a-z0-9\-]+$/",
            "title" => "required|string|max:2000",
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
            "domains" => "required|array|min:1",
            "domains.*" => "required|string|max:255",
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            "domains.0" => __("module_event.domain"),
        ];
    }

    /**
     * @return void
     */
    public function addDomain(): void
    {
        $this->domains[] = "";
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
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function save()
    {
        $this->authorize(PermissionEnum::EVENT_CREATE->value);

        $rules = [
            "slug" => "required|string|max:255|regex:/^[a-z0-9\\-]+$/",
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
            $rules["domains.*"] = "required|string|max:255|unique:domains,domain";
        }
        $valid = $this->validate($rules, [
            "slug.required" => __("validation.required", ["attribute" => __("module_event.slug")]),
            "slug.regex" => __("module_event.slug_format"),
            "title.required" => __("validation.required", ["attribute" => __("module_event.title")]),
            "icon.image" => __("module_event.icon_must_be_image"),
            "domains.required" => __("module_event.domain_required"),
            "domains.*.unique" => __("validation.unique", ["attribute" => __("module_event.domain")]),
        ]);

        $domainStrings = config("tenancy.type") === "path"
            ? [trim($valid["slug"])]
            : array_values(array_filter(array_map("trim", $valid["domains"] ?? [])));
        if (empty($domainStrings)) {
            $this->addError("domains.0", __("module_event.domain_required"));
            return;
        }

        $slug = trim($valid["slug"]);
        if (Tenant::query()->where("id", $slug)->exists()) {
            $this->addError("slug", __("validation.unique", ["attribute" => __("module_event.slug")]));
            return;
        }

        $iconPath = null;
        if ($this->icon) {
            $iconPath = $this->icon->store("tenant-icons", "public");
        }

        $faviconIcoPath = null;

        if ($this->faviconIco) {

            $faviconIcoPath = $this->faviconIco->store("tenant-icons", "public");
        }

        $faviconPngPath = null;
        if ($this->faviconPng) {
            $faviconPngPath = $this->faviconPng->store("tenant-icons", "public");
        }

        $brandLightPath = null;
        if ($this->brandLight) {
            $brandLightPath = $this->brandLight->store("tenant-icons", "public");
        }

        $brandDarkPath = null;
        if ($this->brandDark) {
            $brandDarkPath = $this->brandDark->store("tenant-icons", "public");
        }

        DB::beginTransaction();
        try {
            $tenantData = [
                "id" => $slug,
                "title" => trim($valid["title"]),
                "description" => trim((string) ($valid["description"] ?? "")) ?: null,
                "event_start_date" => $this->eventStartDate ?: null,
                "event_end_date" => $this->eventEndDate ?: null,
                "event_start_time" => $this->eventStartTime ?: null,
                "event_end_time" => $this->eventEndTime ?: null,
                "icon" => $iconPath,
                "favicon_ico" => $faviconIcoPath,
                "favicon_png" => $faviconPngPath,
                "brand_light" => $brandLightPath,
                "brand_dark" => $brandDarkPath,
                "primary_color" => $this->primaryColor ?: null,
                "secondary_color" => $this->secondaryColor ?: null,
                "tertiary_color" => $this->tertiaryColor ?: null,
            ];
            $tenantData["add_ons_features"] = $this->addOnsFeatures;
            $tenantData["add_ons_modules"] = $this->addOnsModules;
            $tenantData["add_ons_config"] = $this->buildAddOnsConfig();
            $tenant = Tenant::create($tenantData);
            foreach ($domainStrings as $domain) {
                $tenant->domains()->create(["domain" => $domain]);
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

            tenancy()->initialize($tenant);
            $adminUser->assignRole(RoleEnum::ADMIN->value);
            tenancy()->end();

            DB::commit();
            session()->flash("message", __("module_event.created_successfully"));
            return redirect()->to(tenant_routes("admin.tenants.events.index"));
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
        return view("livewire.admin.event.create")
            ->layout("layouts.app", [
            "title" => __("module_event.create_event"),
            "showSidebar" => true,
        ]);
    }
}
