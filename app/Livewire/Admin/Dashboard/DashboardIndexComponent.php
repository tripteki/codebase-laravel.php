<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Setting;
use App\Models\User;
use Livewire\Component;
use Illuminate\View\View;

class DashboardIndexComponent extends Component
{
    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $totalSettings = Setting::query()->count();
        $activeSettings = Setting::query()->withoutTrashed()->count();
        $deletedSettings = Setting::query()->onlyTrashed()->count();

        $totalUsers = User::query()->count();
        $verifiedUsers = User::query()->whereNotNull('email_verified_at')->count();
        $unverifiedUsers = User::query()->whereNull('email_verified_at')->count();
        $deletedUsers = User::query()->onlyTrashed()->count();
        $usersWithProfile = User::query()->whereHas('profile')->count();
        $usersWithoutProfile = User::query()->whereDoesntHave('profile')->count();

        return view("livewire.admin.dashboard.index", [
            "systemStats" => [
                "total_settings" => $totalSettings,
                "active_settings" => $activeSettings,
                "deleted_settings" => $deletedSettings,
            ],
            "userStats" => [
                "total_users" => $totalUsers,
                "verified_users" => $verifiedUsers,
                "unverified_users" => $unverifiedUsers,
                "deleted_users" => $deletedUsers,
                "users_with_profile" => $usersWithProfile,
                "users_without_profile" => $usersWithoutProfile,
            ],
        ])->layout("layouts.app", [
            "title" => __("common.dashboard"),
        ]);
    }
}
