<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Stage\StageMeetingPermissionEnum;
use App\Models\StageMeeting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class MeetingIndexDataTableComponent extends DataTableComponent
{
    /**
     * @var class-string<\App\Models\StageMeeting>
     */
    protected $model = StageMeeting::class;

    /**
     * @return void
     */
    public function configure(): void
    {
        $this->setPrimaryKey("id");
        $this
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setPerPage(10)
            ->setPaginationEnabled()
            ->setSearchEnabled()
            ->setColumnSelectDisabled()
            ->setDefaultSort("created_at", "desc")
            ->setAdditionalSelects([
                "stage_meetings.id",
            ]);
    }

    /**
     * @return Builder
     */
    public function builder(): Builder
    {
        return StageMeeting::query()
            ->with([
                "invitation.user",
                "exhibitorSponsors",
            ])
            ->select("id", "room_id", "title", "start_at", "end_at");
    }

    /**
     * @return string
     */
    public function customView(): string
    {
        return "livewire.admin.stage.meeting.partials.delete-modal";
    }

    /**
     * @return array<int, \Rappasoft\LaravelLivewireTables\Views\Column>
     */
    public function columns(): array
    {
        return [
            Column::make(__("module_stage.column_id"), "room_id")
                ->sortable(),

            Column::make(__("module_stage.column_title"), "title")
                ->sortable()
                ->searchable(),

            Column::make(__("module_stage.delegates"))
                ->label(function (StageMeeting $row) {
                    $u = $row->invitation?->user;
                    if (! $u) {
                        return "-";
                    }

                    $name = e($u->name ?: $u->email ?: "-");
                    $email = e($u->email ?: "");
                    $url = e(tenant_routes("admin.users.show", $u));

                    return '<a href="' . $url . '" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark hover:underline">'
                        . $name
                        . "</a>";
                })
                ->html(),

            Column::make(__("module_stage.exhibitors_sponsors"))
                ->label(function (StageMeeting $row) {
                    $users = $row->exhibitorSponsors ?? collect();
                    if ($users->isEmpty()) {
                        return "-";
                    }

                    $max = 3;
                    $shown = $users->take($max);
                    $more = $users->count() - $shown->count();

                    $html = '<div class="flex flex-wrap gap-1.5">';
                    foreach ($shown as $u) {
                        $name = e($u->name ?: $u->email);
                        $email = e($u->email ?: "");
                        $url = e(tenant_routes("admin.users.show", $u));

                        $html .= '<a href="' . $url . '" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark hover:underline">'
                            . $name
                            . "</a>";
                    }
                    if ($more > 0) {
                        $html .= '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-secondary dark:badge-secondary-dark">+'
                            . e((string) $more)
                            . "</span>";
                    }
                    $html .= "</div>";

                    return $html;
                })
                ->html(),

            Column::make(__("module_stage.column_start_at"), "start_at")
                ->sortable()
                ->format(fn ($value) => $value?->format("Y-m-d g:i A") ?? "-"),

            Column::make(__("module_stage.column_end_at"), "end_at")
                ->sortable()
                ->format(fn ($value) => $value?->format("Y-m-d g:i A") ?? "-"),

            Column::make(__("module_base.column_actions"))
                ->label(fn (StageMeeting $row) => view("livewire.admin.stage.meeting.partials.actions", ["meeting" => $row]))
                ->html(),
        ];
    }

    /**
     * @param string $meetingId
     * @return void
     */
    public function confirmDelete(string $meetingId): void
    {
        $meeting = StageMeeting::query()->findOrFail($meetingId);

        $this->dispatch("open-delete-modal", [
            "meetingId" => $meetingId,
            "meetingTitle" => $meeting->title,
        ]);
    }

    /**
     * @param array|string $data
     * @return void
     */
    public function deleteMeeting(array|string $data): void
    {
        $meetingId = is_array($data) ? ($data["meetingId"] ?? null) : $data;

        if (! $meetingId) {
            return;
        }

        DB::beginTransaction();

        try {
            $meeting = StageMeeting::query()->findOrFail($meetingId);
            $this->authorize(StageMeetingPermissionEnum::STAGE_MEETING_DELETE->value, $meeting);
            $meeting->delete();
            DB::commit();
            session()->flash("message", __("module_stage.deleted_successfully"));
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash("error", __("module_stage.deleted_failed"));
        }
    }
}
