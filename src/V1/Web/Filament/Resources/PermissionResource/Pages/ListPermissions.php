<?php

namespace Src\V1\Web\Filament\Resources\PermissionResource\Pages;

use Src\V1\Web\Filament\Resources\PermissionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListPermissions extends ListRecords
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

            Actions\CreateAction::make()->label(__("module.permission.labels.new")),
        ];
    }
}
