<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Stage\StageMeetingPermissionEnum;
use Illuminate\View\View;
use Livewire\Component;

class MeetingIndexComponent extends Component
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(StageMeetingPermissionEnum::STAGE_MEETING_VIEW->value);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.stage.meeting.index")->layout("layouts.app", [
            "title" => __("module_stage.meeting_title"),
            "showSidebar" => true,
        ]);
    }
}
