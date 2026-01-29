<?php

namespace App\Livewire\Admin\Setting;

use Livewire\Component;
use Illuminate\View\View;

class UserSettingIndexComponent extends Component
{
    /**
     * The active tab.
     *
     * @var string
     */
    public string $activeTab = "personal";

    /**
     * Mount the component.
     *
     * @param string|null $tab
     * @return void
     */
    public function mount(?string $tab = null): void
    {
        if ($tab && in_array($tab, ["personal", "system"])) {
            $this->activeTab = $tab;
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.setting.index")
            ->layout("layouts.app", [
                "title" => __("module_setting.title"),
            ]);
    }
}
