<?php

namespace App\Livewire\Admin\Setting;

use Livewire\Component;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Src\V1\Api\Acl\Enums\RoleEnum;

class UserSettingIndexComponent extends Component
{
    /**
     * @var string
     */
    public string $activeTab = "personal";

    /**
     * @param string|null $tab
     * @return \Illuminate\Http\RedirectResponse|\Livewire\Features\SupportRedirects\Redirector|null
     */
    public function mount(?string $tab = null): RedirectResponse|Redirector|null
    {
        if ($tab && in_array($tab, ["personal", "system"])) {

            $canSeeSystem = $this->canSeeSystemTab();

            if ($tab === "system" && ! $canSeeSystem) {

                return redirect()->to(tenant_routes("admin.settings.tab", ["tab" => "personal"]));
            }

            $this->activeTab = $tab;
        }

        return null;
    }

    /**
     * @return bool
     */
    protected function canSeeSystemTab(): bool
    {
        $user = auth()->user();

        if (! $user || hasTenant()) return false;
        return $user->hasRole(RoleEnum::SUPERADMIN->value) || $user->hasRole(RoleEnum::ADMIN->value);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $canSeeSystemTab = $this->canSeeSystemTab();

        return view("livewire.admin.setting.index", [
            "canSeeSystemTab" => $canSeeSystemTab,
        ])->layout("layouts.app", [
            "title" => __("module_setting.title"),
            "showSidebar" => true,
        ]);
    }
}
