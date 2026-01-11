<?php

namespace Src\V1\Web\Filament\Resources\PermissionResource\Forms;

use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Illuminate\Validation\Rule;

abstract class PermissionForm
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
                "max:64",
                ! $exception ? Rule::unique(Permission::class) : Rule::unique(Permission::class)->ignore($exception),
            ],

            "guard_name" => [

                "required",
                "string",
                "in:" . GuardEnum::WEB->value . "," . GuardEnum::API->value,
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

            "name" => Forms\Components\TextInput::make("name")->label(__("module.permission.labels.name"))->validationAttribute(__("module.permission.labels.name"))->
                prefixIcon("heroicon-s-key")->
                required()->string()->
                minLength(2)->
                maxLength(64)->
                unique(ignoreRecord: true),

            "guard_name" => Forms\Components\Select::make("guard_name")->label(__("module.permission.labels.guard_name"))->validationAttribute(__("module.permission.labels.guard_name"))->
                prefixIcon("heroicon-s-shield-check")->
                required()->
                options([
                    GuardEnum::WEB->value => __("module.permission.options.guard_web"),
                    GuardEnum::API->value => __("module.permission.options.guard_api"),
                ])->
                default(GuardEnum::API->value),

        ][$form];
    }
}
