<?php

namespace Src\V1\Web\Filament\Resources;

use App\Models\User;
use Src\V1\Api\User\Enums\PermissionEnum;
use Src\V1\Web\Filament\Resources\UserResource\Forms\UserForm;
use Src\V1\Web\Filament\Imports\UserImporter;
use Src\V1\Web\Filament\Exports\UserExporter;
use Src\V1\Web\Filament\Resources\UserResource\Pages;
use Src\V1\Web\Filament\Resources\UserResource\RelationManagers;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    /**
     * @var string|null
     */
    protected static ?string $slug = "users";

    /**
     * @var string|null
     */
    protected static ?string $model = User::class;

    /**
     * @var string|null
     */
    protected static ?string $recordTitleAttribute = "email";

    /**
     * @var int
     */
    protected static int $globalSearchResultsLimit = 10;

    /**
     * @var string|null
     */
    protected static ?string $navigationIcon = "heroicon-o-user-group";

    /**
     * @var string|null
     */
    protected static ?string $activeNavigationIcon = "heroicon-s-user-group";

    /**
     * @var int
     */
    protected static ?int $navigationSort = 10;

    /**
     * @return string
     */
    public static function getLabel(): string
    {
        return __("module.user.label");
    }

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __("module.user.navigation_group");
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __("module.user.navigation");
    }

    /**
     * @return string|null
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return "success";
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
        return parent::getEloquentQuery()->
            withoutGlobalScopes([

                SoftDeletingScope::class,
            ]);
    }

    /**
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->
            schema([

                Forms\Components\Wizard::make([

                    Forms\Components\Wizard\Step::make(__("module.user.steps.information"))->icon("heroicon-s-information-circle")->schema([

                        UserForm::form("name"),
                        UserForm::form("email"),
                    ]),

                    Forms\Components\Wizard\Step::make(__("module.user.steps.credential"))->icon("heroicon-s-shield-check")->schema([

                        UserForm::form("password"),
                        UserForm::form("password_confirmation"),
                    ]),

                    Forms\Components\Wizard\Step::make(__("module.user.steps.roles"))->icon("heroicon-s-shield-check")->schema([

                        UserForm::form("roles"),
                    ]),

                    Forms\Components\Wizard\Step::make(__("module.user.steps.log_activities"))->icon("heroicon-s-document-text")->schema([

                        UserForm::form("log_activities"),
                    ]),
                ])->persistStepInQueryString(),
            ])->columns(1);
    }

    /**
     * @param \Filament\Tables\Table $table
     * @return \Filament\Tables\Table
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->
            heading(__("module.user.tables.heading"))->
            description(__("module.user.tables.description"))->
            columns([

                Tables\Columns\TextColumn::make("name")->label(__("module.user.labels.name"))->
                    sortable()->
                    searchable()->
                    icon("heroicon-s-at-symbol")->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("email")->label(__("module.user.labels.email"))->
                    sortable()->
                    searchable()->
                    copyable()->
                    icon("heroicon-s-envelope")->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("roles.name")->label(__("module.user.labels.roles"))->
                    listWithLineBreaks()->
                    limitList(3)->
                    expandableLimitedList()->
                    icon("heroicon-s-shield-check")->
                    color("success")->
                    alignment(Alignment::Start),
                Tables\Columns\TextColumn::make("created_at")->label(__("module.user.labels.created_at"))->since()->
                    sortable()->
                    alignment(Alignment::Justify),
                Tables\Columns\TextColumn::make("updated_at")->label(__("module.user.labels.updated_at"))->since()->
                    sortable()->
                    alignment(Alignment::Justify),

            ])->defaultSort("email")->filters([

                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make("id")->label(__("module.user.labels.id")),
                Tables\Filters\Filter::make("name")->label(__("module.user.labels.name")),
                Tables\Filters\Filter::make("email")->label(__("module.user.labels.email")),
                Tables\Filters\Filter::make("created_at")->label(__("module.user.labels.created_at")),
                Tables\Filters\Filter::make("updated_at")->label(__("module.user.labels.updated_at")),
                Tables\Filters\Filter::make("deleted_at")->label(__("module.user.labels.deleted_at")),

            ])->headerActions([

                Tables\Actions\ImportAction::make()->importer(UserImporter::class)->chunkSize(50)->
                    icon("heroicon-o-arrow-down-circle")->
                    visible(fn () => auth()->user()->can(PermissionEnum::USER_IMPORT_CREATE->value)),
                Tables\Actions\ExportAction::make()->exporter(UserExporter::class)->
                    icon("heroicon-o-arrow-up-circle")->
                    visible(fn () => auth()->user()->can(PermissionEnum::USER_EXPORT_CREATE->value)),

            ])->recordUrl(null)->actions([

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [

            "index" => Pages\ListUsers::route("/"),
            "edit" => Pages\EditUser::route("/{record}/edit"),
            "view" => Pages\ViewUser::route("/{record}"),
        ];
    }

    /**
     * @return bool
     */
    public static function canViewAny(): bool
    {
        return auth()->user()->can(PermissionEnum::USER_VIEW->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canView(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::USER_VIEW->value);
    }

    /**
     * @return bool
     */
    public static function canCreate(): bool
    {
        return auth()->user()->can(PermissionEnum::USER_CREATE->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::USER_UPDATE->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::USER_DELETE->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canForceDelete(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::USER_FORCE_DELETE->value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return bool
     */
    public static function canRestore(Model $record): bool
    {
        return auth()->user()->can(PermissionEnum::USER_RESTORE->value);
    }
}
