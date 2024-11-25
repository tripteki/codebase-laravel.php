<?php

namespace Src\V1\Web\Filament\Resources\UserResource\Pages;

use Src\V1\Web\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;

class ListUsers extends ListRecords
{
    /**
     * @var string
     */
    protected static string $resource = UserResource::class;

    /**
     * @return array
     */
    protected function getHeaderWidgets(): array
    {
        return [

            UserResource\Widgets\UserOverview::class,
        ];
    }

    /**
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [

            Actions\CreateAction::make()->using(function (array $userData, string $model) {

                $user = $model::create($userData);
                $user->markEmailAsVerified();

                return $user;
            }),
        ];
    }
}
