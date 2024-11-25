<?php

namespace Src\V1\Web\Filament\Resources\UserResource\Pages;

use Src\V1\Web\Filament\Resources\UserResource;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Infolists;

class ViewUser extends ViewRecord
{
    /**
     * @var string
     */
    protected static string $resource = UserResource::class;

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

                Infolists\Components\TextEntry::make("id")->label(__("module.user.labels.id"))->columnSpan(3)->badge()->
                    copyable()->
                    icon("heroicon-s-identification")->
                    alignment(Alignment::Justify),
                Infolists\Components\TextEntry::make("name")->label(__("module.user.labels.name"))->columnSpan(3)->badge()->
                    icon("heroicon-s-at-symbol")->
                    alignment(Alignment::Justify),
                Infolists\Components\TextEntry::make("email")->label(__("module.user.labels.email"))->columnSpan(3)->badge()->
                    copyable()->
                    icon("heroicon-s-envelope")->
                    alignment(Alignment::Justify),
                Infolists\Components\TextEntry::make("created_at")->label(__("module.user.labels.created_at"))->columnSpan(1)->badge()->
                    dateTime()->
                    dateTimeTooltip()->
                    icon("heroicon-s-calendar")->
                    alignment(Alignment::Justify),
                Infolists\Components\TextEntry::make("updated_at")->label(__("module.user.labels.updated_at"))->columnSpan(1)->badge()->
                    dateTime()->
                    dateTimeTooltip()->
                    icon("heroicon-s-arrow-path")->
                    alignment(Alignment::Justify),
                Infolists\Components\TextEntry::make("deleted_at")->label(__("module.user.labels.deleted_at"))->columnSpan(1)->badge()->
                    placeholder("Null")->
                    dateTime()->
                    dateTimeTooltip()->
                    icon("heroicon-s-trash")->
                    alignment(Alignment::Justify),
            ]);
    }
}
