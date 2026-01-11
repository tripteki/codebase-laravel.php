<?php

namespace Src\V1\Web\Filament\Resources\ActivityResource\Pages;

use Src\V1\Web\Filament\Resources\ActivityResource;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Infolists;

class ViewActivity extends ViewRecord
{
    /**
     * @var string
     */
    protected static string $resource = ActivityResource::class;

    /**
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [

            Actions\DeleteAction::make(),
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

                Infolists\Components\TextEntry::make("id")->label(__("module.activity.labels.id"))->columnSpan(3)->badge()->
                    color("info")->
                    copyable()->
                    icon("heroicon-s-identification")->
                    alignment(Alignment::Start),
                Infolists\Components\TextEntry::make("event")->label(__("module.activity.labels.event"))->columnSpan(1)->badge()->
                    icon("heroicon-s-bolt")->
                    color("warning")->
                    placeholder("—")->
                    alignment(Alignment::Start),
                Infolists\Components\TextEntry::make("causer.name")->label(__("module.activity.labels.causer"))->columnSpan(2)->badge()->
                    icon("heroicon-s-user")->
                    color("gray")->
                    default(__("module.activity.labels.system"))->
                    alignment(Alignment::Start),
                Infolists\Components\TextEntry::make("properties")->label(__("module.activity.labels.properties"))->columnSpan(3)->
                    icon("heroicon-s-code-bracket")->
                    html()->
                    getStateUsing(function ($record) {
                        $properties = $record->properties;
                        $json = is_array($properties) ? json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $properties;
                        return '<pre class="text-xs overflow-auto p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">'.htmlspecialchars($json).'</pre>';
                    })->
                    alignment(Alignment::Start),
                Infolists\Components\TextEntry::make("description")->label(__("module.activity.labels.description"))->columnSpan(3)->badge()->
                    icon("heroicon-s-document-text")->
                    color("primary")->
                    alignment(Alignment::Start),
                Infolists\Components\TextEntry::make("created_at")->label(__("module.activity.labels.created_at"))->columnSpan(2)->badge()->
                    dateTime()->
                    dateTimeTooltip()->
                    icon("heroicon-s-calendar")->
                    alignment(Alignment::Start),
            ]);
    }
}
