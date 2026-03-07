<?php

namespace App\Livewire\Admin\Activity;

use Src\V1\Api\Log\Enums\PermissionEnum;
use Src\V1\Api\Log\Models\Activity;
use Illuminate\View\View;
use Livewire\Component;

class ActivityShowComponent extends Component
{
    /**
     * @var \Src\V1\Api\Log\Models\Activity
     */
    public Activity $activity;

    /**
     * @param \Src\V1\Api\Log\Models\Activity $activity
     * @return void
     */
    public function mount(Activity $activity): void
    {
        $this->authorize(PermissionEnum::ACTIVITY_VIEW->value);

        $relations = ["causer", "subject"];
        if (config("tenancy.is_tenancy")) {
            $relations[] = "tenant.domains";
        }
        $this->activity = $activity->load($relations);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.activity.show", [
            "activity" => $this->activity,
        ])->layout("layouts.app", [
            "title" => __("module_activity.show_title"),
            "showSidebar" => true,
        ]);
    }
}
