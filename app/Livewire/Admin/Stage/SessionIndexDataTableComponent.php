<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Stage\StageSessionPermissionEnum;
use App\Models\StageSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SessionIndexDataTableComponent extends DataTableComponent
{
    /**
     * @var class-string<\App\Models\StageSession>
     */
    protected $model = StageSession::class;

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
                "stage_sessions.id",
            ]);
    }

    /**
     * @return Builder
     */
    public function builder(): Builder
    {
        return StageSession::query()
            ->with(["speakers.user"])
            ->select("id", "room_id", "title", "description", "start_at", "end_at", "created_at");
    }

    /**
     * @return string
     */
    public function customView(): string
    {
        return "livewire.admin.stage.session.partials.delete-modal";
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

            Column::make(__("module_stage.speakers"))
                ->label(function (StageSession $row) {
                    $speakers = $row->speakers ?? collect();
                    if ($speakers->isEmpty()) {
                        return "-";
                    }

                    $html = '<div class="flex flex-wrap gap-1.5">';
                    foreach ($speakers as $invitation) {
                        $u = $invitation->user;
                        if (! $u) {
                            continue;
                        }
                        $name = e($u->name ?: $u->email ?: "-");
                        $url = e(tenant_routes("admin.users.show", $u));
                        $html .= '<a href="' . $url . '" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark hover:underline">'
                            . $name
                            . "</a>";
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
                ->label(fn (StageSession $row) => view("livewire.admin.stage.session.partials.actions", ["session" => $row]))
                ->html(),
        ];
    }

    /**
     * @param string $sessionId
     * @return void
     */
    public function confirmDelete(string $sessionId): void
    {
        $session = StageSession::query()->findOrFail($sessionId);

        $this->dispatch("open-delete-modal", [
            "sessionId" => $sessionId,
            "sessionTitle" => $session->title,
        ]);
    }

    /**
     * @param array|string $data
     * @return void
     */
    public function deleteSession(array|string $data): void
    {
        $sessionId = is_array($data) ? ($data["sessionId"] ?? null) : $data;

        if (! $sessionId) {
            return;
        }

        DB::beginTransaction();

        try {
            $session = StageSession::query()->findOrFail($sessionId);
            $this->authorize(StageSessionPermissionEnum::STAGE_SESSION_DELETE->value, $session);
            $session->delete();
            DB::commit();
            session()->flash("message", __("module_stage.deleted_successfully"));
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash("error", __("module_stage.deleted_failed"));
        }
    }
}
