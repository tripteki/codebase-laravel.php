<?php

namespace App\Livewire\Admin\Permission;

use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Permission;
use Illuminate\View\View;
use Livewire\Component;

class PermissionShowComponent extends Component
{
    /**
     * @var \Src\V1\Api\Acl\Models\Permission
     */
    public Permission $permission;

    /**
     * @param \Src\V1\Api\Acl\Models\Permission $permission
     * @return void
     */
    public function mount(Permission $permission): void
    {
        $this->authorize(PermissionEnum::PERMISSION_VIEW->value);

        $this->permission = $permission;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.permission.show", [
            "permission" => $this->permission,
        ])->layout("layouts.app", [
            "title" => __("module_permission.show_title"),
        ]);
    }
}
