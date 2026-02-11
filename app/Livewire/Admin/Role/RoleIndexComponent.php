<?php

namespace App\Livewire\Admin\Role;

use Src\V1\Api\Acl\Enums\PermissionEnum;
use Illuminate\View\View;
use Livewire\Component;

class RoleIndexComponent extends Component
{
    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::ROLE_VIEW->value);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.role.index")
            ->layout("layouts.app", [
                "title" => __("module_role.title"),
            ]);
    }
}
