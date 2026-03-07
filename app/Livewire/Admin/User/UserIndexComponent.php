<?php

namespace App\Livewire\Admin\User;

use Src\V1\Api\User\Enums\PermissionEnum;
use Illuminate\View\View;
use Livewire\Component;

class UserIndexComponent extends Component
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::USER_VIEW->value);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.user.index")
            ->layout("layouts.app", [
                "title" => __("module_user.title"),
                "showSidebar" => true,
            ]);
    }
}
