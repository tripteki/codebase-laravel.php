<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Stage\StageSessionPermissionEnum;
use App\Models\StageSession;
use Illuminate\View\View;
use Livewire\Component;

class SessionShowComponent extends Component
{
    /**
     * @var \App\Models\StageSession
     */
    public StageSession $session;

    /**
     * @param \App\Models\StageSession $session
     * @return void
     */
    public function mount(StageSession $session): void
    {
        $this->authorize(StageSessionPermissionEnum::STAGE_SESSION_VIEW->value, $session);
        $this->session = $session->load(["attachments", "speakers.user"]);
    }

    /**
     * @param string $id
     * @return void
     */
    public function removeAttachment(string $id): void
    {
        $attachment = $this->session->attachments()->findOrFail($id);
        \Illuminate\Support\Facades\Storage::disk($attachment->getStorageDisk())->delete($attachment->getStoragePath());
        $attachment->delete();
        $this->session->refresh();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.stage.session.show", [
            "session" => $this->session,
        ])->layout("layouts.app", [
            "title" => __("module_stage.view_session"),
            "showSidebar" => true,
        ]);
    }
}
