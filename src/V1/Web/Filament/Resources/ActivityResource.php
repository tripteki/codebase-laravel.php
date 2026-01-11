<?php

namespace Src\V1\Web\Filament\Resources;

use Src\V1\Api\Log\Models\Activity;
use Src\V1\Api\Log\Enums\PermissionEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Web\Filament\Resources\ActivityResource\Pages;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActivityResource extends Resource
{
    /**
     * @var string|null
     */
    protected static ?string $slug = "activities";

    /**
     * @var string|null
     */
    protected static ?string $model = Activity::class;

    /**
     * @var string|null
     */
    protected static ?string $recordTitleAttribute = "description";

    /**
     * @var int
     */
    protected static int $globalSearchResultsLimit = 10;

    /**
     * @var string|null
     */
    protected static ?string $navigationIcon = "heroicon-o-clipboard-document-list";

    /**
     * @var string|null
     */
    protected static ?string $activeNavigationIcon = "heroicon-s-clipboard-document-list";

    /**
     * @var int
     */
    protected static ?int $navigationSort = 30;

    /**
     * @return string
     */
    public static function getLabel(): string
    {
        return __("module.activity.label");
    }

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __("module.activity.navigation_group");
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __("module.activity.navigation");
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
        $query = static::getModel()::query();

        if (! auth()->user()->hasRole(RoleEnum::SUPERADMIN->value)) {
            $query->where('causer_id', auth()->id());
        }

        return $query->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()->hasRole(RoleEnum::SUPERADMIN->value)) {
            $query->where('causer_id', auth()->id());
        }

        return $query;
    }

    /**
     * @param \Filament\Tables\Table $table
     * @return \Filament\Tables\Table
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->
            heading(__("module.activity.tables.heading"))->
            description(__("module.activity.tables.description"))->
            columns([

                Tables\Columns\TextColumn::make("id")->label(__("module.activity.labels.id"))->
                    searchable()->
                    sortable()->
                    badge()->
                    color("info")->
                    copyable()->
                    icon("heroicon-s-identification")->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("event")->label(__("module.activity.labels.event"))->
                    searchable()->
                    sortable()->
                    badge()->
                    color("warning")->
                    icon("heroicon-s-bolt")->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("causer.name")->label(__("module.activity.labels.causer"))->
                    searchable()->
                    sortable()->
                    badge()->
                    color("gray")->
                    icon("heroicon-s-user")->
                    default(__("module.activity.labels.system"))->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("description")->label(__("module.activity.labels.description"))->
                    searchable()->
                    sortable()->
                    badge()->
                    color("primary")->
                    icon("heroicon-s-document-text")->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("created_at")->label(__("module.activity.labels.created_at"))->since()->
                    sortable()->
                    alignment(Alignment::Justify),
            ])->defaultSort("created_at", "desc")->filters([

                Tables\Filters\Filter::make("id")->label(__("module.activity.labels.id")),
                Tables\Filters\Filter::make("event")->label(__("module.activity.labels.event")),
                Tables\Filters\Filter::make("description")->label(__("module.activity.labels.description")),
                Tables\Filters\Filter::make("created_at")->label(__("module.activity.labels.created_at")),

            ])->actions([

                Tables\Actions\ViewAction::make(),
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
    public static function getRelations(): array
    {
        return [

            //
        ];
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [

            "index" => Pages\ListActivities::route("/"),
            "view" => Pages\ViewActivity::route("/{record}"),
        ];
    }

    /**
     * @return bool
     */
    public static function canViewAny(): bool
    {
        return auth()->user()->can(PermissionEnum::ACTIVITY_VIEW->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canView(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::ACTIVITY_VIEW->value);
    }

    /**
     * @return bool
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::ACTIVITY_DELETE->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canForceDelete(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::ACTIVITY_DELETE->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canRestore(Model $record): bool
    {
        return false;
    }
}
