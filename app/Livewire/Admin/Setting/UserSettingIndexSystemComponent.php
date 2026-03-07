<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class UserSettingIndexSystemComponent extends Component
{
    use WithFileUploads;

    /**
     * @var array
     */
    public array $settingModalRows = [];

    /**
     * @var array<int, TemporaryUploadedFile|null>
     */
    public array $settingValueFiles = [];

    /**
     * @var string|null
     */
    public ?string $currentTenantId = null;

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->currentTenantId = config("tenancy.is_tenancy") && tenant() ? tenant()->id : null;

        $tenancyWasInitialized = false;

        if (config("tenancy.is_tenancy") && filled($this->currentTenantId)) {
            $tenant = Tenant::find($this->currentTenantId);
            if ($tenant) {
                tenancy()->initialize($tenant);
                $tenancyWasInitialized = true;
            }
        }

        try {
            $this->resetValidation();
            $this->settingValueFiles = [];
            $this->settingModalRows = $this->buildSettingModalRowsFromDb();
            if ($this->settingModalRows === []) {
                $this->settingModalRows[] = $this->blankSettingModalRow();
            }
        } finally {
            if ($tenancyWasInitialized) {
                tenancy()->end();
            }
        }
    }

    /**
     * @return array{id: null, key: string, value: string, value_kind: string}
     */
    protected function blankSettingModalRow(): array
    {
        return [
            "id" => null,
            "key" => "",
            "value" => "",
            "value_kind" => "text",
        ];
    }

    /**
     * @return array<int, array{id: string|null, key: string, value: string, value_kind: string}>
     */
    protected function buildSettingModalRowsFromDb(): array
    {
        $list = [];
        foreach (Setting::query()->orderBy("key")->get() as $setting) {
            $value = (string) ($setting->value ?? "");
            $list[] = [
                "id" => (string) $setting->getKey(),
                "key" => (string) $setting->key,
                "value" => $value,
                "value_kind" => $this->inferSettingValueKind($value),
            ];
        }

        return $list;
    }

    /**
     * @param string $storedValue
     * @return string
     */
    protected function inferSettingValueKind(string $storedValue): string
    {
        return str_starts_with($storedValue, "setting-files/") ? "file" : "text";
    }

    /**
     * @return string
     */
    protected function settingFileStoreDirectory(): string
    {
        $suffix = filled($this->currentTenantId) ? (string) $this->currentTenantId : "central";

        return "setting-files/" . $suffix;
    }

    /**
     * @return void
     */
    public function addSettingRow(): void
    {
        $this->settingModalRows[] = $this->blankSettingModalRow();
    }

    /**
     * @param int $index
     * @return void
     */
    public function removeSettingRow(int $index): void
    {
        $row = $this->settingModalRows[$index] ?? null;
        if ($row === null) {
            return;
        }

        $tenancyWasInitialized = false;
        if (config("tenancy.is_tenancy") && filled($this->currentTenantId)) {
            $tenant = Tenant::find($this->currentTenantId);
            if ($tenant) {
                tenancy()->initialize($tenant);
                $tenancyWasInitialized = true;
            }
        }

        try {
            if ($row["id"] !== null && $row["id"] !== "") {
                $model = Setting::query()->whereKey((string) $row["id"])->first();
                if ($model !== null) {
                    $raw = (string) ($model->value ?? "");
                    if ($raw !== "" && str_starts_with($raw, "setting-files/")) {
                        Storage::disk("public")->delete($raw);
                    }
                    $model->delete();
                }
            }
        } finally {
            if ($tenancyWasInitialized) {
                tenancy()->end();
            }
        }

        unset($this->settingModalRows[$index]);
        $this->settingModalRows = array_values($this->settingModalRows);
        $this->settingValueFiles = [];

        if ($this->settingModalRows === []) {
            $this->settingModalRows[] = $this->blankSettingModalRow();
        }
    }

    /**
     * @return void
     */
    public function saveSystemSettings(): void
    {
        $this->resetValidation();

        $tenancyWasInitialized = false;
        if (config("tenancy.is_tenancy") && filled($this->currentTenantId)) {
            $tenant = Tenant::find($this->currentTenantId);
            if ($tenant) {
                tenancy()->initialize($tenant);
                $tenancyWasInitialized = true;
            }
        }

        try {
            $hasRowValidationError = false;
            foreach ($this->settingModalRows as $i => $row) {
                $k = trim((string) ($row["key"] ?? ""));
                $rowValueRaw = (string) ($row["value"] ?? "");
                $upload = $this->settingValueFiles[$i] ?? null;
                $hasUpload = $upload instanceof TemporaryUploadedFile;
                $kind = (($row["value_kind"] ?? "text") === "file") ? "file" : "text";

                if ($k === "" && trim($rowValueRaw) === "" && ! $hasUpload) {
                    continue;
                }

                if ($k === "") {
                    $this->addError("settingModalRows.$i.key", __("validation.required"));
                    $hasRowValidationError = true;
                }

                if ($kind === "file" && $k !== "" && ! $hasUpload && trim($rowValueRaw) === "") {
                    $this->addError("settingModalRows.$i.value", __("module_content.value_file_required"));
                    $hasRowValidationError = true;
                }
            }

            if ($hasRowValidationError) {
                return;
            }

            $this->validate([
                "settingValueFiles.*" => ["nullable", "file", "max:10240"],
            ]);

            foreach ($this->settingModalRows as $i => $row) {
                $k = trim((string) ($row["key"] ?? ""));
                $rowValueRaw = (string) ($row["value"] ?? "");
                $upload = $this->settingValueFiles[$i] ?? null;
                $hasUpload = $upload instanceof TemporaryUploadedFile;
                $kind = (($row["value_kind"] ?? "text") === "file") ? "file" : "text";

                if ($k === "" && trim($rowValueRaw) === "" && ! $hasUpload) {
                    continue;
                }

                $id = $row["id"] ?? null;

                $previousFromDb = "";
                if ($id !== null && $id !== "") {
                    $existingRow = Setting::query()->whereKey((string) $id)->first();
                    if ($existingRow !== null) {
                        $previousFromDb = (string) ($existingRow->value ?? "");
                    }
                }

                if ($kind === "text") {
                    $v = trim($rowValueRaw);
                    if ($previousFromDb !== "" && str_starts_with($previousFromDb, "setting-files/") && $previousFromDb !== $v) {
                        Storage::disk("public")->delete($previousFromDb);
                    }
                } elseif ($hasUpload) {
                    $v = $upload->store($this->settingFileStoreDirectory(), "public");
                    if ($previousFromDb !== "" && str_starts_with($previousFromDb, "setting-files/") && $previousFromDb !== $v) {
                        Storage::disk("public")->delete($previousFromDb);
                    }
                } else {
                    $v = trim($rowValueRaw);
                }

                if ($k === "" && $v === "") {
                    continue;
                }

                $duplicateQuery = Setting::query()->where("key", $k);
                if ($id !== null && $id !== "") {
                    $duplicateQuery->where("id", "!=", $id);
                }

                if ($duplicateQuery->exists()) {
                    $this->addError("settingModalRows.$i.key", __("validation.unique", ["attribute" => __("module_setting.key")]));

                    return;
                }

                if ($id !== null && $id !== "") {
                    $setting = Setting::query()->whereKey((string) $id)->first();

                    if ($setting === null) {
                        continue;
                    }

                    $setting->update([
                        "key" => $k,
                        "value" => $v !== "" ? $v : null,
                    ]);
                } else {
                    Setting::query()->create([
                        "key" => $k,
                        "value" => $v !== "" ? $v : null,
                    ]);
                }
            }

            $this->settingValueFiles = [];
            $this->settingModalRows = $this->buildSettingModalRowsFromDb();
            if ($this->settingModalRows === []) {
                $this->settingModalRows[] = $this->blankSettingModalRow();
            }

            session()->flash("message", __("module_setting.system_updated_successfully"));
        } finally {
            if ($tenancyWasInitialized) {
                tenancy()->end();
            }
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.setting.setting-system-component");
    }
}
