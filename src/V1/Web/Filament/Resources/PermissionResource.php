<?php

namespace Src\V1\Web\Filament\Resources;

use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Web\Filament\Resources\PermissionResource\Forms\PermissionForm;
use Src\V1\Web\Filament\Imports\PermissionImporter;
use Src\V1\Web\Filament\Exports\PermissionExporter;
use Src\V1\Web\Filament\Resources\PermissionResource\Pages;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class PermissionResource extends Resource
{
    /**
     * @var string|null
     */
    protected static ?string $slug = "permissions";

    /**
     * @var string|null
     */
    protected static ?string $model = Permission::class;

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
    protected static ?string $navigationIcon = "heroicon-o-key";

    /**
     * @var string|null
     */
    protected static ?string $activeNavigationIcon = "heroicon-s-key";

    /**
     * @var int
     */
    protected static ?int $navigationSort = 21;

    /**
     * @return string
     */
    public static function getLabel(): string
    {
        return __("module.permission.label");
    }

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __("module.permission.navigation_group");
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __("module.permission.navigation");
    }

    /**
     * @return string|null
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return "info";
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

                Forms\Components\Section::make(__("module.permission.sections.basic_info"))
                    ->description(__("module.permission.sections.basic_info_description"))
                    ->icon("heroicon-o-information-circle")
                    ->schema([

                        PermissionForm::form("name"),
                        PermissionForm::form("guard_name"),
                    ])->columns(2),
            ]);
    }

    /**
     * @param \Filament\Tables\Table $table
     * @return \Filament\Tables\Table
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->
            heading(__("module.permission.tables.heading"))->
            description(__("module.permission.tables.description"))->
            columns([

                Tables\Columns\TextColumn::make("name")->label(__("module.permission.labels.name"))->
                    sortable()->
                    searchable()->
                    icon("heroicon-s-key")->
                    badge()->
                    color("warning")->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("guard_name")->label(__("module.permission.labels.guard_name"))->
                    sortable()->
                    searchable()->
                    icon("heroicon-s-shield-check")->
                    badge()->
                    color("gray")->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("created_at")->label(__("module.permission.labels.created_at"))->since()->
                    sortable()->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("updated_at")->label(__("module.permission.labels.updated_at"))->since()->
                    sortable()->
                    alignment(Alignment::Justify),

            ])->defaultSort("name")->filters([

                Tables\Filters\Filter::make("name")->label(__("module.permission.labels.name")),
                Tables\Filters\Filter::make("guard_name")->label(__("module.permission.labels.guard_name")),
                Tables\Filters\Filter::make("created_at")->label(__("module.permission.labels.created_at")),
                Tables\Filters\Filter::make("updated_at")->label(__("module.permission.labels.updated_at")),

            ])->headerActions([

                Tables\Actions\ImportAction::make()->importer(PermissionImporter::class)->chunkSize(50)->
                    icon("heroicon-o-arrow-down-circle")->
                    visible(fn () => auth()->user()->can(PermissionEnum::PERMISSION_IMPORT_CREATE->value)),
                Tables\Actions\ExportAction::make()->exporter(PermissionExporter::class)->
                    icon("heroicon-o-arrow-up-circle")->
                    visible(fn () => auth()->user()->can(PermissionEnum::PERMISSION_EXPORT_CREATE->value)),

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

            "index" => Pages\ListPermissions::route("/"),
            "edit" => Pages\EditPermission::route("/{record}/edit"),
            "view" => Pages\ViewPermission::route("/{record}"),
        ];
    }

    /**
     * @return bool
     */
    public static function canViewAny(): bool
    {
        return auth()->user()->can(PermissionEnum::PERMISSION_VIEW->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canView(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::PERMISSION_VIEW->value);
    }

    /**
     * @return bool
     */
    public static function canCreate(): bool
    {
        return auth()->user()->can(PermissionEnum::PERMISSION_CREATE->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::PERMISSION_UPDATE->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::PERMISSION_DELETE->value);
    }
}
