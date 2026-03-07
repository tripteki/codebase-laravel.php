<?php

namespace App\Livewire\Admin\Event;

use App\Enum\Tenant\PermissionEnum;
use Illuminate\View\View;
use Livewire\Component;

class EventIndexComponent extends Component
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::EVENT_VIEW->value);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.event.index")
            ->layout("layouts.app", [
                "title" => __("module_event.module_title"),
                "showSidebar" => true,
            ]);
    }
}
