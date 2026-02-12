<?php

namespace App\Livewire\Admin\Activity;

use Src\V1\Api\Log\Enums\PermissionEnum;
use Illuminate\View\View;
use Livewire\Component;

class ActivityIndexComponent extends Component
{
    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::ACTIVITY_VIEW->value);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.activity.index")
            ->layout("layouts.app", [
                "title" => __("module_activity.title"),
            ]);
    }
}
