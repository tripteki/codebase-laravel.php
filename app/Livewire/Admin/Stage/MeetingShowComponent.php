<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Stage\StageMeetingPermissionEnum;
use App\Models\StageMeeting;
use Illuminate\View\View;
use Livewire\Component;

class MeetingShowComponent extends Component
{
    /**
     * @var \App\Models\StageMeeting
     */
    public StageMeeting $meeting;

    /**
     * @param \App\Models\StageMeeting $meeting
     * @return void
     */
    public function mount(StageMeeting $meeting): void
    {
        $this->authorize(StageMeetingPermissionEnum::STAGE_MEETING_VIEW->value, $meeting);
        $this->meeting = $meeting->load(["attachments", "invitation.user.profile", "exhibitorSponsors.profile"]);
    }

    /**
     * @param string $id
     * @return void
     */
    public function removeAttachment(string $id): void
    {
        $attachment = $this->meeting->attachments()->findOrFail($id);
        \Illuminate\Support\Facades\Storage::disk($attachment->getStorageDisk())->delete($attachment->getStoragePath());
        $attachment->delete();
        $this->meeting->refresh();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.stage.meeting.show", [
            "meeting" => $this->meeting,
        ])->layout("layouts.app", [
            "title" => __("module_stage.view_meeting"),
            "showSidebar" => true,
        ]);
    }
}
