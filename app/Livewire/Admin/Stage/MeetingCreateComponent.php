<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Stage\StageMeetingPermissionEnum;
use App\Enum\Stage\StageMeetingStatusEnum;
use App\Models\Profile;
use App\Models\StageInvitation;
use App\Models\StageMeeting;
use App\Models\User;
use App\Notifications\StageMeetingDelegateNotification;
use App\Notifications\StageMeetingExhibitorSponsorNotification;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;

class MeetingCreateComponent extends Component
{
    use WithFileUploads;

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
     * @var string|null
     */
    public ?string $delegate_id = null;

    /**
     * @var array<int, string>
     */
    public array $exhibitor_sponsor_ids = [];

    /**
     * @var string|null
     */
    public $currentTenantId = null;

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(StageMeetingPermissionEnum::STAGE_MEETING_CREATE->value);
        $this->currentTenantId = config("tenancy.is_tenancy") && tenant() ? tenant()->id : null;

        $user = auth()->user();
        if ($user && $user->hasRole(RoleEnum::DELEGATE->value)) {
            $this->delegate_id = (string) $user->id;
        }
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
            "delegate_id" => "required|exists:users,id",
            "exhibitor_sponsor_ids" => "required|array|min:1",
            "exhibitor_sponsor_ids.*" => "exists:users,id",
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            "delegate_id" => __("module_stage.validation_attribute_delegate"),
            "exhibitor_sponsor_ids" => __("module_stage.validation_attribute_exhibitor_sponsors"),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            "delegate_id.required" => __("module_stage.validation_delegate_required"),
            "exhibitor_sponsor_ids.required" => __("module_stage.validation_exhibitor_sponsors_required"),
            "exhibitor_sponsor_ids.min" => __("module_stage.validation_exhibitor_sponsors_min"),
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
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $user = auth()->user();
        if ($user && $user->hasRole(RoleEnum::DELEGATE->value)) {
            $this->delegate_id = (string) $user->id;
        }

        $this->validate($this->rules(), $this->messages(), $this->validationAttributes());

        $tenantId = null;
        if (config("tenancy.is_tenancy")) {
            if (filled($this->currentTenantId)) {
                $tenantId = $this->currentTenantId;
            } elseif (tenancy()->initialized) {
                $tenantId = tenant("id");
            }
        }

        $meeting = new StageMeeting();
        if ($tenantId !== null) {
            $meeting->tenant_id = $tenantId;
        }
        $meeting->title = $this->title;
        $meeting->description = $this->description ?: null;
        $meeting->start_at = ($this->startDate && $this->startTime) ? \Carbon\Carbon::parse($this->startDate . " " . $this->startTime) : ($this->startDate ? \Carbon\Carbon::parse($this->startDate) : null);
        $meeting->end_at = ($this->endDate && $this->endTime) ? \Carbon\Carbon::parse($this->endDate . " " . $this->endTime) : ($this->endDate ? \Carbon\Carbon::parse($this->endDate) : null);
        $meeting->save();

        foreach ($this->attachments as $file) {
            $path = $file->store("stage_meetings/" . $meeting->id, "public");

            $meeting->attachments()->create([
                "tenant_id" => config("tenancy.is_tenancy") ? ($meeting->tenant_id ?? $tenantId) : null,
                "disk" => "public",
                "path" => $path,
                "original_name" => $file->getClientOriginalName(),
                "mime_type" => $file->getMimeType(),
                "size" => $file->getSize(),
            ]);
        }

        if ($this->delegate_id) {
            StageInvitation::create([
                "tenant_id" => config("tenancy.is_tenancy") ? ($meeting->tenant_id ?? $tenantId) : null,
                "invitationable_type" => StageMeeting::class,
                "invitationable_id" => $meeting->id,
                "role" => StageInvitation::ROLE_DELEGATE,
                "user_id" => $this->delegate_id,
                "staged" => StageMeetingStatusEnum::defaultStaged(),
            ]);
            $delegateUser = User::find($this->delegate_id);
            if ($delegateUser) {
                $delegateUser->notify(new StageMeetingDelegateNotification($meeting, url(tenant_routes("admin.stage.meetings.show", $meeting))));
            }
        }
        foreach ($this->exhibitor_sponsor_ids ?? [] as $userId) {
            StageInvitation::create([
                "tenant_id" => config("tenancy.is_tenancy") ? ($meeting->tenant_id ?? $tenantId) : null,
                "invitationable_type" => StageMeeting::class,
                "invitationable_id" => $meeting->id,
                "role" => StageInvitation::ROLE_EXHIBITOR_SPONSOR,
                "user_id" => $userId,
                "staged" => StageMeetingStatusEnum::defaultStaged(),
            ]);
            $exhibitorSponsorUser = User::find($userId);
            if ($exhibitorSponsorUser) {
                $exhibitorSponsorUser->notify(new StageMeetingExhibitorSponsorNotification($meeting, url(tenant_routes("admin.stage.meetings.show", $meeting))));
            }
        }

        session()->flash("message", __("module_stage.created_successfully"));

        return redirect()->to(tenant_routes("admin.stage.meetings.show", $meeting));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $guard = GuardEnum::WEB->value;

        $delegateUsers = User::query()
            ->with("profile")
            ->whereHas("roles", fn ($q) => $q->where("name", RoleEnum::DELEGATE->value)->where("guard_name", $guard))
            ->orderBy("name")
            ->get();
        $exhibitorSponsorUsers = User::query()
            ->with("profile")
            ->whereHas("roles", fn ($q) => $q->whereIn("name", [RoleEnum::EXHIBITOR->value, RoleEnum::SPONSOR->value])->where("guard_name", $guard))
            ->orderBy("name")
            ->get();

        $availableInterests = Profile::query()
            ->whereNotNull("interests")
            ->get()
            ->pluck("interests")
            ->flatten()
            ->unique()
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        $currentUser = auth()->user();
        $hideDelegateSelect = $currentUser && $currentUser->hasRole(RoleEnum::DELEGATE->value);

        return view("livewire.admin.stage.meeting.create", [
            "delegateUsers" => $delegateUsers,
            "exhibitorSponsorUsers" => $exhibitorSponsorUsers,
            "availableInterests" => $availableInterests,
            "eventStartDate" => $this->getTenantEventStartDate(),
            "eventEndDate" => $this->getTenantEventEndDate(),
            "hideDelegateSelect" => $hideDelegateSelect,
        ])->layout("layouts.app", [
            "title" => __("module_stage.create_meeting"),
            "showSidebar" => true,
        ]);
    }
}
