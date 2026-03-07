<?php

namespace App\Livewire\Admin\Permission;

use Src\V1\Api\Acl\Enums\PermissionEnum;
use Illuminate\View\View;
use Livewire\Component;

class PermissionIndexComponent extends Component
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::PERMISSION_VIEW->value);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.permission.index")
            ->layout("layouts.app", [
                "title" => __("module_permission.title"),
                "showSidebar" => true,
            ]);
    }
}
