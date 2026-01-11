<?php

namespace Src\V1\Web\Filament\Resources\RoleResource\Pages;

use Src\V1\Web\Filament\Resources\RoleResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListRoles extends ListRecords
{
    /**
     * @var string
     */
    protected static string $resource = RoleResource::class;

    /**
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [

            Actions\CreateAction::make()->label(__("module.role.labels.new")),
        ];
    }
}
