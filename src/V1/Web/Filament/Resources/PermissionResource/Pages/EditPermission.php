<?php

namespace Src\V1\Web\Filament\Resources\PermissionResource\Pages;

use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Web\Filament\Resources\PermissionResource;
use Src\V1\Web\Filament\Resources\PermissionResource\Forms\PermissionForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Forms;

class EditPermission extends EditRecord
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

            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public function form(Forms\Form $form): Forms\Form
    {
        return $form->
            schema([

                Forms\Components\Section::make(__("module.permission.sections.basic_info"))->
                    schema([

                        PermissionForm::form("name"),
                        PermissionForm::form("guard_name"),

                    ])->columns(2),

            ]);
    }
}
