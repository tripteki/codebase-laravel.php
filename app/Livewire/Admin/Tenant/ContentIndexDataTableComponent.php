<?php

namespace App\Livewire\Admin\Tenant;

use App\Models\ContentLocale;
use App\Enum\Tenant\PermissionEnum;
use Locale as PhpLocale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Src\V1\Api\I18N\Services\I18NService;

class ContentIndexDataTableComponent extends DataTableComponent
{
    /**
     * @var string
     */
    protected $model = ContentLocale::class;

    /**
     * @return void
     */
    public function configure(): void
    {
        $this->setPrimaryKey("code");

        $this
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setPerPage(10)
            ->setPaginationEnabled()
            ->setSearchEnabled()
            ->setColumnSelectDisabled()
            ->setDefaultSort("code", "asc")
            ->setAdditionalSelects(["code"]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function builder(): Builder
    {
        if (! auth()->user()?->can(PermissionEnum::EVENT_VIEW->value)) {
            return ContentLocale::query()->whereRaw("1 = 0");
        }

        $codes = app(I18NService::class)->availableLangs();
        sort($codes);

        if (count($codes) === 0) {
            return ContentLocale::query()->whereRaw("1 = 0");
        }

        $first = $codes[0];
        $query = DB::query()->selectRaw("? as `code`", [$first]);

        for ($i = 1; $i < count($codes); $i++) {
            $query->unionAll(DB::query()->selectRaw("? as `code`", [$codes[$i]]));
        }

        return ContentLocale::query()->fromSub($query, "content_locales");
    }

    /**
     * @return array<int, \Rappasoft\LaravelLivewireTables\Views\Column>
     */
    public function columns(): array
    {
        return [
            Column::make(__("module_content.locale_code"), "code")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<span class="text-gray-900 dark:text-gray-200">' . e((string) $value) . "</span>";
                })
                ->html(),

            Column::make(__("module_content.locale_label"), "code")
                ->label(function ($row) {
                    $code = (string) ($row->code ?? "");

                    return '<span class="text-gray-900 dark:text-gray-200">' . e($this->localeDisplayName($code)) . "</span>";
                })
                ->html(),

            Column::make(__("module_base.column_actions"))
                ->label(function ($row) {
                    return view("livewire.admin.tenant.partials.actions", [
                        "code" => (string) ($row->code ?? ""),
                    ]);
                })
                ->html(),
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    protected function localeDisplayName(string $code): string
    {
        if (class_exists(PhpLocale::class)) {
            $name = PhpLocale::getDisplayName($code, app()->getLocale());
            if ($name !== "") {
                return $name;
            }
        }

        return $code;
    }
}
