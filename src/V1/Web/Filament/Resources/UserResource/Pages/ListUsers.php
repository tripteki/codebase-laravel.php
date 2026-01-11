<?php

namespace Src\V1\Web\Filament\Resources\UserResource\Pages;

use Src\V1\Web\Filament\Resources\UserResource;
use Src\V1\Web\Filament\Resources\UserResource\Widgets\UserStatsWidget;
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

            UserStatsWidget::class,
        ];
    }

    /**
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [

            Actions\CreateAction::make()->label(__("module.user.labels.new"))->createAnother(false)->using(function (array $data, string $model): Model {

                $user = $model::create($data);
                $user->markEmailAsVerified();

                return $user;
            }),
        ];
    }
}
