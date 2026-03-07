<?php

namespace App\Livewire\Admin\Event;

use App\Models\Tenant;
use App\Enum\Tenant\PermissionEnum;
use Illuminate\View\View;
use Livewire\Component;

class EventShowComponent extends Component
{
    /**
     * @var \App\Models\Tenant
     */
    public Tenant $tenant;

    /**
     * @param \App\Models\Tenant $tenant
     * @return void
     */
    public function mount(Tenant $tenant): void
    {
        $this->authorize(PermissionEnum::EVENT_VIEW->value);

        $this->tenant = $tenant->load("domains");
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.event.show", [
            "tenant" => $this->tenant,
        ])->layout("layouts.app", [
            "title" => __("module_event.show_title"),
            "showSidebar" => true,
        ]);
    }
}
