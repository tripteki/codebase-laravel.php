<?php

namespace Src\V1\Web\Filament\Resources\UserResource\Forms;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Illuminate\Validation\Rule;

abstract class UserForm
{
    /**
     * @param string $validation
     * @param mixed|null $exception
     * @return array
     */
    public static function validation(string $validation, $exception = null): array
    {
        return [

            "name" => [

                "required",
                "string",
                "min:2",
                "max:16",
                ! $exception ? Rule::unique(User::class) : Rule::unique(User::class)->ignore($exception),
            ],

            "email" => [

                "required",
                "string",
                "min:8",
                "max:48",
                "email",
                ! $exception ? Rule::unique(User::class) : Rule::unique(User::class)->ignore($exception),
            ],

            "password" => [

                "required",
                "string",
                "min:8",
                "max:16",
            ],

            "password_confirmation" => [

                "string",
                "same:password",
            ],

        ][$validation];
    }

    /**
     * @param string $form
     * @return \Filament\Forms\Components\Component
     */
    public static function form(string $form): Component
    {
        return [

            "name" => Forms\Components\TextInput::make("name")->label(__("module.user.labels.name"))->validationAttribute(__("module.user.labels.name"))->
                prefixIcon("heroicon-s-at-symbol")->
                required()->string()->
                minLength(2)->
                maxLength(16)->
                unique(ignoreRecord: true),

            "email" => Forms\Components\TextInput::make("email")->label(__("module.user.labels.email"))->validationAttribute(__("module.user.labels.email"))->
                prefixIcon("heroicon-s-envelope")->
                required()->string()->
                minLength(8)->
                maxLength(48)->
                email()->
                unique(ignoreRecord: true),

            "password" => Forms\Components\TextInput::make("password")->label(__("module.user.labels.password"))->validationAttribute(__("module.user.labels.password"))->
                revealable()->
                dehydrateStateUsing(fn (string $state): string => $state)->
                dehydrated(fn (?string $state): bool => filled($state))->
                required(fn (string $context): bool => $context === "create")->string()->
                password()->
                confirmed()->
                minLength(8)->
                maxLength(16),

            "password_confirmation" => Forms\Components\TextInput::make("password_confirmation")->label(__("module.user.labels.password_confirmation"))->validationAttribute(__("module.user.labels.password_confirmation"))->
                revealable()->
                required(fn (string $context): bool => $context === "create")->string()->
                password(),

        ][$form];
    }
}
