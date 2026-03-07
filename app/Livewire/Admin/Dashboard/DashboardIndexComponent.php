<?php

namespace App\Livewire\Admin\Dashboard;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;

class DashboardIndexComponent extends Component
{
    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $user = auth()->user();

        $isCentral = ! hasTenant();
        $isTenantAdmin = $user && $user->hasRole(RoleEnum::ADMIN->value);

        $showUsersByRole = $isCentral || $isTenantAdmin;
        $pulseEnabled = (bool) config("pulse.enabled", true);
        $canViewPulse = $user && Gate::forUser($user)->allows("viewPulse");
        $showPulseSection = $isCentral && $pulseEnabled;
        $showPulseMetricsEmbed = $showPulseSection && $canViewPulse;
        $showNotificationsPanel = hasTenant();

        $usersByRoleLabels = [];
        $usersByRoleSeries = [];

        if ($showUsersByRole) {
            foreach (
                Role::query()
                    ->where("guard_name", GuardEnum::WEB->value)
                    ->orderBy("name")
                    ->withCount("users")
                    ->get() as $role
            ) {
                $usersByRoleLabels[] = $role->name;
                $usersByRoleSeries[] = (int) $role->users_count;
            }
        }

        $pulsePath = trim((string) config("pulse.path", "monitor"), "/");
        $pulseUrl = url($pulsePath === "" ? "/monitor" : "/" . $pulsePath);
        $pulseMetricsUrl = $showPulseMetricsEmbed ? tenant_routes("admin.dashboard.pulse-metrics") : "";

        return view("livewire.admin.dashboard.index", [
            "showUsersByRole" => $showUsersByRole,
            "showPulseSection" => $showPulseSection,
            "showPulseMetricsEmbed" => $showPulseMetricsEmbed,
            "showNotificationsPanel" => $showNotificationsPanel,
            "usersByRoleLabels" => $usersByRoleLabels,
            "usersByRoleSeries" => $usersByRoleSeries,
            "pulseUrl" => $pulseUrl,
            "pulseMetricsUrl" => $pulseMetricsUrl,
        ])->layout("layouts.app", [
            "title" => __("common.dashboard"),
            "showSidebar" => true,
        ]);
    }
}
