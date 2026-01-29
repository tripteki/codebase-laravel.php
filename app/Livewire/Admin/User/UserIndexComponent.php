<?php

namespace App\Livewire\Admin\User;

use Illuminate\View\View;
use Livewire\Component;

class UserIndexComponent extends Component
{
    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.user.index")
            ->layout("layouts.app", [
                "title" => __("module_user.title"),
            ]);
    }
}
