<?php

namespace Src\V1\Web\Filament\Resources\RoleResource\Pages;

use Src\V1\Api\Acl\Models\Role;
use Src\V1\Web\Filament\Resources\RoleResource;
use Src\V1\Web\Filament\Resources\RoleResource\Forms\RoleForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Forms;

class EditRole extends EditRecord
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

                Forms\Components\Section::make(__("module.role.sections.basic_info"))->schema([
                    RoleForm::form("name"),
                    RoleForm::form("guard_name"),
                ])->columns(2),

                Forms\Components\Section::make(__("module.role.sections.permissions"))->schema([
                    RoleForm::form("permissions"),
                ])->columns(1)->extraAttributes([
                    'class' => 'overflow-y-auto',
                    'style' => 'max-height: 500px;',
                ]),

            ])->columns(1);
    }
}
