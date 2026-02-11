<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use Livewire\Component;
use Illuminate\View\View;

class UserSettingIndexSystemComponent extends Component
{
    /**
     * @var array<int, array{key: string, value: string}>
     */
    public $settings = [];

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $allSettings = Setting::query()->get();

        if ($allSettings->isEmpty()) {
            $this->settings[] = [
                "id" => null,
                "key" => "",
                "value" => "",
            ];
        } else {
            foreach ($allSettings as $setting) {
                $this->settings[] = [
                    "id" => $setting->id,
                    "key" => $setting->key,
                    "value" => $setting->value,
                ];
            }
        }
    }

    /**
     * Add a new setting row.
     *
     * @return void
     */
    public function addSetting(): void
    {
        $this->settings[] = [
            "id" => null,
            "key" => "",
            "value" => "",
        ];
    }

    /**
     * Remove a setting row.
     *
     * @param int $index
     * @return void
     */
    public function removeSetting(int $index): void
    {
        $setting = $this->settings[$index] ?? null;

        if ($setting && isset($setting["id"])) {
            Setting::query()->where("id", $setting["id"])->delete();
        }

        unset($this->settings[$index]);
        $this->settings = array_values($this->settings);
    }

    /**
     * Validation rules.
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "settings.*.key" => ["required", "string", "max:255"],
            "settings.*.value" => ["nullable", "string"],
        ];
    }

    /**
     * Save the settings.
     *
     * @return void
     */
    public function save(): void
    {
        $this->validate();

        foreach ($this->settings as $settingData) {
            if (isset($settingData["id"])) {
                $setting = Setting::query()->find($settingData["id"]);
                if ($setting) {
                    $setting->update([
                        "key" => $settingData["key"],
                        "value" => $settingData["value"] ?? null,
                    ]);
                }
            } else {
                Setting::query()->create([
                    "key" => $settingData["key"],
                    "value" => $settingData["value"] ?? null,
                ]);
            }
        }

        session()->flash("message", __("module_setting.system_updated_successfully"));
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.setting.setting-system-component");
    }
}
