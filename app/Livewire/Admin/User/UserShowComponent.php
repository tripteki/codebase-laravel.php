<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class UserShowComponent extends Component
{
    /**
     * @var \App\Models\User
     */
    public User $user;

    /**
     * @param \App\Models\User $user
     * @return void
     */
    public function mount(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.user.show", [
            "user" => $this->user,
        ])->layout("layouts.app", [
            "title" => __("module_user.show_title"),
        ]);
    }
}
