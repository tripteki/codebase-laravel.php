<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Stage\StageSessionPermissionEnum;
use App\Enum\Stage\StageSessionStatusEnum;
use App\Models\StageInvitation;
use App\Models\StageSession;
use App\Models\User;
use App\Notifications\StageSessionSpeakerNotification;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;

class SessionEditComponent extends Component
{
    use WithFileUploads;

    /**
     * @var \App\Models\StageSession
     */
    public StageSession $session;

    /**
     * @var string
     */
    public string $title = "";

    /**
     * @var string
     */
    public string $description = "";

    /**
     * @var string|null
     */
    public ?string $startDate = null;

    /**
     * @var string|null
     */
    public ?string $startTime = null;

    /**
     * @var string|null
     */
    public ?string $endDate = null;

    /**
     * @var string|null
     */
    public ?string $endTime = null;

    /**
     * @var \Illuminate\Http\UploadedFile[]
     */
    public array $attachments = [];

    /**
     * @var \Illuminate\Http\UploadedFile[]
     */
    public array $newAttachmentInput = [];

    /**
     * @var array<int, string>
     */
    public array $speaker_ids = [];

    /**
     * @param \App\Models\StageSession $session
     * @return void
     */
    public function mount(StageSession $session): void
    {
        $this->authorize(StageSessionPermissionEnum::STAGE_SESSION_UPDATE->value, $session);
        $this->session = $session->load(["attachments", "speakers"]);
        $this->title = $this->session->title;
        $this->description = (string) ($this->session->description ?? "");
        $this->startDate = $this->session->start_at?->format("Y-m-d") ?? null;
        $this->startTime = $this->session->start_at?->format("H:i") ?? null;
        $this->endDate = $this->session->end_at?->format("Y-m-d") ?? null;
        $this->endTime = $this->session->end_at?->format("H:i") ?? null;
        $this->speaker_ids = $this->session->speakers->pluck("user_id")->map(fn ($id) => (string) $id)->values()->all();
    }

    /**
     * @return string
     */
    protected function attachmentFileRule(): string
    {
        $min = (int) config("attachments.min_file_size", 0);
        $max = (int) config("attachments.max_file_size", 102400);
        $mimes = "jpg,jpeg,png,gif,webp,bmp,svg,pdf,doc,docx,xls,xlsx,txt,mp4,webm,mp3,wav,zip";
        $base = "file|max:{$max}|mimes:{$mimes}";

        return $min > 0 ? "file|min:{$min}|max:{$max}|mimes:{$mimes}" : $base;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $eventStart = $this->getTenantEventStartDate();
        $eventEnd = $this->getTenantEventEndDate();

        $startDateRules = ["nullable", "date"];
        $endDateRules = ["nullable", "date"];

        if ($eventStart) {
            $startDateRules[] = "after_or_equal:" . $eventStart;
            $endDateRules[] = "after_or_equal:" . $eventStart;
        }

        if ($eventEnd) {
            $startDateRules[] = "before_or_equal:" . $eventEnd;
            $endDateRules[] = "before_or_equal:" . $eventEnd;
        }

        return [
            "title" => "required|string|max:255",
            "description" => "nullable|string|max:5000",
            "startDate" => implode("|", $startDateRules),
            "startTime" => "nullable|date_format:H:i",
            "endDate" => implode("|", $endDateRules),
            "endTime" => "nullable|date_format:H:i",
            "attachments" => "nullable|array",
            "attachments.*" => $this->attachmentFileRule(),
            "speaker_ids" => "required|array|min:1",
            "speaker_ids.*" => "exists:users,id",
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            "speaker_ids" => __("module_stage.validation_attribute_speaker"),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            "speaker_ids.required" => __("module_stage.validation_speaker_required"),
            "speaker_ids.min" => __("module_stage.validation_speaker_required"),
        ];
    }

    /**
     * @return string|null
     */
    protected function getTenantEventStartDate(): ?string
    {
        $t = config("tenancy.is_tenancy") ? tenant() : null;
        $val = $t ? $t->getAttribute("event_start_date") : null;

        if ($val instanceof \DateTimeInterface) {
            $val = $val->format("Y-m-d");
        } elseif ($val !== null) {
            $val = (string) $val;
        }

        return is_string($val) && preg_match("/^\\d{4}-\\d{2}-\\d{2}$/", $val) ? $val : null;
    }

    /**
     * @return string|null
     */
    protected function getTenantEventEndDate(): ?string
    {
        $t = config("tenancy.is_tenancy") ? tenant() : null;
        $val = $t ? $t->getAttribute("event_end_date") : null;

        if ($val instanceof \DateTimeInterface) {
            $val = $val->format("Y-m-d");
        } elseif ($val !== null) {
            $val = (string) $val;
        }

        return is_string($val) && preg_match("/^\\d{4}-\\d{2}-\\d{2}$/", $val) ? $val : null;
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
     * @return void
     */
    public function updatedNewAttachmentInput(): void
    {
        if (empty($this->newAttachmentInput)) {
            return;
        }
        $this->attachments = array_values(array_merge($this->attachments, $this->newAttachmentInput));
    }

    /**
     * @param int $index
     * @return void
     */
    public function removeNewAttachment(int $index): void
    {
        if (isset($this->attachments[$index])) {
            $arr = $this->attachments;
            unset($arr[$index]);
            $this->attachments = array_values($arr);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $user = auth()->user();
        if ($user && $user->hasRole(RoleEnum::SPEAKER->value)) {
            $this->speaker_ids = array_values(array_unique(array_merge($this->speaker_ids ?? [], [(string) $user->id])));
        }

        $this->validate($this->rules(), $this->messages(), $this->validationAttributes());

        $tenantId = null;
        if (config("tenancy.is_tenancy") && tenancy()->initialized) {
            $tenantId = tenant("id");
        }
        if (config("tenancy.is_tenancy") && $tenantId !== null) {
            $this->session->tenant_id = $this->session->tenant_id ?? $tenantId;
        }
        $this->session->title = $this->title;
        $this->session->description = $this->description ?: null;
        $this->session->start_at = ($this->startDate && $this->startTime) ? \Carbon\Carbon::parse($this->startDate . " " . $this->startTime) : ($this->startDate ? \Carbon\Carbon::parse($this->startDate) : null);
        $this->session->end_at = ($this->endDate && $this->endTime) ? \Carbon\Carbon::parse($this->endDate . " " . $this->endTime) : ($this->endDate ? \Carbon\Carbon::parse($this->endDate) : null);
        $this->session->save();

        foreach ($this->attachments as $file) {
            $path = $file->store("stage_sessions/" . $this->session->id, "public");

            $this->session->attachments()->create([
                "tenant_id" => config("tenancy.is_tenancy") ? ($this->session->tenant_id ?? $tenantId) : null,
                "disk" => "public",
                "path" => $path,
                "original_name" => $file->getClientOriginalName(),
                "mime_type" => $file->getMimeType(),
                "size" => $file->getSize(),
            ]);
        }

        $this->session->invitations()->where("role", StageInvitation::ROLE_SPEAKER)->delete();
        foreach (array_unique($this->speaker_ids ?? []) as $userId) {
            StageInvitation::create([
                "tenant_id" => config("tenancy.is_tenancy") ? ($this->session->tenant_id ?? $tenantId) : null,
                "invitationable_type" => StageSession::class,
                "invitationable_id" => $this->session->id,
                "role" => StageInvitation::ROLE_SPEAKER,
                "user_id" => $userId,
                "staged" => StageSessionStatusEnum::defaultStaged(),
            ]);
            $speakerUser = User::find($userId);
            if ($speakerUser) {
                $speakerUser->notify(new StageSessionSpeakerNotification($this->session, url(tenant_routes("admin.stage.sessions.show", $this->session))));
            }
        }

        session()->flash("message", __("module_stage.updated_successfully"));

        return redirect()->to(tenant_routes("admin.stage.sessions.show", $this->session));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $guard = GuardEnum::WEB->value;

        $speakerUsers = User::query()
            ->with("profile")
            ->whereHas("roles", fn ($q) => $q->where("name", RoleEnum::SPEAKER->value)->where("guard_name", $guard))
            ->orderBy("name")
            ->get();

        $currentUser = auth()->user();
        $hideSpeakerSelect = $currentUser && $currentUser->hasRole(RoleEnum::SPEAKER->value);

        return view("livewire.admin.stage.session.edit", [
            "speakerUsers" => $speakerUsers,
            "eventStartDate" => $this->getTenantEventStartDate(),
            "eventEndDate" => $this->getTenantEventEndDate(),
            "hideSpeakerSelect" => $hideSpeakerSelect,
        ])->layout("layouts.app", [
            "title" => __("module_stage.edit_session"),
            "showSidebar" => true,
        ]);
    }
}
