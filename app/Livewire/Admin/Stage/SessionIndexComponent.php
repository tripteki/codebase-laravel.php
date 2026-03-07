<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Stage\StageSessionPermissionEnum;
use Illuminate\View\View;
use Livewire\Component;

class SessionIndexComponent extends Component
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(StageSessionPermissionEnum::STAGE_SESSION_VIEW->value);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.stage.session.index")->layout("layouts.app", [
            "title" => __("module_stage.session_title"),
            "showSidebar" => true,
        ]);
    }
}
