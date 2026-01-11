<?php

namespace Src\V1\Web\Filament\Resources\ActivityResource\Pages;

use Src\V1\Web\Filament\Resources\ActivityResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListActivities extends ListRecords
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

            //
        ];
    }
}
