<?php

namespace Src\V1\Web\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Src\V1\Web\Filament\Resources\UserResource;
use Src\V1\Web\Filament\Resources\UserResource\Forms\UserForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Forms;

class EditUser extends EditRecord
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

            Actions\Action::make("verify")->label(__("module.user.labels.email_verified_at"))->icon("heroicon-o-envelope")->requiresConfirmation()->action(function () {

                $this->record->markEmailAsVerified();

                return redirect(UserResource::getUrl());

            })->visible(function () {

                return ! $this->record->hasVerifiedEmail();
            }),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
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

                UserForm::form("name"),
                UserForm::form("email"),
                UserForm::form("password"),
                UserForm::form("password_confirmation"),

            ])->columns(1);
    }
}
