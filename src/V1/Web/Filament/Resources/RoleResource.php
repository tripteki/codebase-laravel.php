<?php

namespace Src\V1\Web\Filament\Resources;

use Src\V1\Api\Acl\Models\Role;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Web\Filament\Resources\RoleResource\Forms\RoleForm;
use Src\V1\Web\Filament\Imports\RoleImporter;
use Src\V1\Web\Filament\Exports\RoleExporter;
use Src\V1\Web\Filament\Resources\RoleResource\Pages;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class RoleResource extends Resource
{
    /**
     * @var string|null
     */
    protected static ?string $slug = "roles";

    /**
     * @var string|null
     */
    protected static ?string $model = Role::class;

    /**
     * @var string|null
     */
    protected static ?string $recordTitleAttribute = "name";

    /**
     * @var int
     */
    protected static int $globalSearchResultsLimit = 10;

    /**
     * @var string|null
     */
    protected static ?string $navigationIcon = "heroicon-o-shield-check";

    /**
     * @var string|null
     */
    protected static ?string $activeNavigationIcon = "heroicon-s-shield-check";

    /**
     * @var int
     */
    protected static ?int $navigationSort = 20;

    /**
     * @return string
     */
    public static function getLabel(): string
    {
        return __("module.role.label");
    }

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __("module.role.navigation_group");
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __("module.role.navigation");
    }

    /**
     * @return string|null
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return "primary";
    }

    /**
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    /**
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->
            schema([

                Forms\Components\Section::make(__("module.role.sections.basic_info"))
                    ->description(__("module.role.sections.basic_info_description"))
                    ->icon("heroicon-o-information-circle")
                    ->schema([

                        RoleForm::form("name"),
                        RoleForm::form("guard_name"),
                    ])->columns(2),

                Forms\Components\Section::make(__("module.role.sections.permissions"))
                    ->description(__("module.role.sections.permissions_description"))
                    ->icon("heroicon-o-key")
                    ->schema([

                        RoleForm::form("permissions"),
                    ])
                    ->extraAttributes([
                        'class' => 'overflow-y-auto',
                        'style' => 'max-height: 500px;',
                    ]),
            ]);
    }

    /**
     * @param \Filament\Tables\Table $table
     * @return \Filament\Tables\Table
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->
            heading(__("module.role.tables.heading"))->
            description(__("module.role.tables.description"))->
            columns([

                Tables\Columns\TextColumn::make("name")->label(__("module.role.labels.name"))->
                    sortable()->
                    searchable()->
                    icon("heroicon-s-tag")->
                    badge()->
                    color("success")->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("guard_name")->label(__("module.role.labels.guard_name"))->
                    sortable()->
                    searchable()->
                    icon("heroicon-s-shield-check")->
                    badge()->
                    color("gray")->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("permissions.name")->label(__("module.role.labels.permissions"))->
                    listWithLineBreaks()->
                    limitList(3)->
                    expandableLimitedList()->
                    icon("heroicon-s-key")->
                    color("warning")->
                    alignment(Alignment::Start),
                Tables\Columns\TextColumn::make("created_at")->label(__("module.role.labels.created_at"))->since()->
                    sortable()->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("updated_at")->label(__("module.role.labels.updated_at"))->since()->
                    sortable()->
                    alignment(Alignment::Justify),

            ])->defaultSort("name")->filters([

                Tables\Filters\Filter::make("name")->label(__("module.role.labels.name")),
                Tables\Filters\Filter::make("guard_name")->label(__("module.role.labels.guard_name")),
                Tables\Filters\Filter::make("created_at")->label(__("module.role.labels.created_at")),
                Tables\Filters\Filter::make("updated_at")->label(__("module.role.labels.updated_at")),

            ])->headerActions([

                Tables\Actions\ImportAction::make()->importer(RoleImporter::class)->chunkSize(50)->
                    icon("heroicon-o-arrow-down-circle")->
                    visible(fn () => auth()->user()->can(PermissionEnum::ROLE_IMPORT_CREATE->value)),
                Tables\Actions\ExportAction::make()->exporter(RoleExporter::class)->
                    icon("heroicon-o-arrow-up-circle")->
                    visible(fn () => auth()->user()->can(PermissionEnum::ROLE_EXPORT_CREATE->value)),

            ])->recordUrl(null)->actions([

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [

            "index" => Pages\ListRoles::route("/"),
            "edit" => Pages\EditRole::route("/{record}/edit"),
            "view" => Pages\ViewRole::route("/{record}"),
        ];
    }

    /**
     * @return bool
     */
    public static function canViewAny(): bool
    {
        return auth()->user()->can(PermissionEnum::ROLE_VIEW->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canView(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::ROLE_VIEW->value);
    }

    /**
     * @return bool
     */
    public static function canCreate(): bool
    {
        return auth()->user()->can(PermissionEnum::ROLE_CREATE->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::ROLE_UPDATE->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::ROLE_DELETE->value);
    }
}
