<?php

namespace Src\V1\Web\Filament\Resources\PermissionResource\Pages;

use Src\V1\Web\Filament\Resources\PermissionResource;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Infolists;

class ViewPermission extends ViewRecord
{
    /**
     * @var string
     */
    protected static string $resource = PermissionResource::class;

    /**
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [

            Actions\EditAction::make(),
        ];
    }

    /**
     * @param \Filament\Infolists\Infolist $infolist
     * @return \Filament\Infolists\Infolist
     */
    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist->
            schema([

                Infolists\Components\TextEntry::make("id")->label(__("module.permission.labels.id"))->columnSpan(3)->badge()->
                    copyable()->
                    icon("heroicon-s-identification")->
                    alignment(Alignment::Start),
                Infolists\Components\TextEntry::make("name")->label(__("module.permission.labels.name"))->columnSpan(1)->badge()->
                    icon("heroicon-s-key")->
                    color("warning")->
                    alignment(Alignment::Start),
                Infolists\Components\TextEntry::make("guard_name")->label(__("module.permission.labels.guard_name"))->columnSpan(2)->badge()->
                    icon("heroicon-s-shield-check")->
                    color("gray")->
                    alignment(Alignment::Start),
                Infolists\Components\TextEntry::make("created_at")->label(__("module.permission.labels.created_at"))->columnSpan(1)->badge()->
                    dateTime()->
                    dateTimeTooltip()->
                    icon("heroicon-s-calendar")->
                    alignment(Alignment::Start),
                Infolists\Components\TextEntry::make("updated_at")->label(__("module.permission.labels.updated_at"))->columnSpan(1)->badge()->
                    dateTime()->
                    dateTimeTooltip()->
                    icon("heroicon-s-arrow-path")->
                    alignment(Alignment::Start),
            ]);
    }
}
