<?php

namespace App\Livewire\Admin\Role;

use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\View\View;
use Livewire\Component;

class RoleShowComponent extends Component
{
    /**
     * @var \Src\V1\Api\Acl\Models\Role
     */
    public Role $role;

    /**
     * @param \Src\V1\Api\Acl\Models\Role $role
     * @return void
     */
    public function mount(Role $role): void
    {
        $this->authorize(PermissionEnum::ROLE_VIEW->value);

        $this->role = $role;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $this->role->load("permissions");

        return view("livewire.admin.role.show", [
            "role" => $this->role,
        ])->layout("layouts.app", [
            "title" => __("module_role.show_title"),
            "showSidebar" => true,
        ]);
    }
}
