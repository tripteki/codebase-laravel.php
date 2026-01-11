<?php

namespace Src\V1\Web\Filament\Resources\RoleResource\Forms;

use Src\V1\Api\Acl\Models\Role;
use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Illuminate\Validation\Rule;

abstract class RoleForm
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
                ! $exception ? Rule::unique(Role::class) : Rule::unique(Role::class)->ignore($exception),
            ],

            "guard_name" => [

                "required",
                "string",
                "in:web,api",
            ],

            "permissions" => [

                "nullable",
                "array",
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

            "name" => Forms\Components\TextInput::make("name")->label(__("module.role.labels.name"))->validationAttribute(__("module.role.labels.name"))->
                prefixIcon("heroicon-s-tag")->
                required()->string()->
                minLength(2)->
                maxLength(64)->
                unique(ignoreRecord: true),

            "guard_name" => Forms\Components\Select::make("guard_name")->label(__("module.role.labels.guard_name"))->validationAttribute(__("module.role.labels.guard_name"))->
                prefixIcon("heroicon-s-shield-check")->
                required()->
                options([
                    GuardEnum::WEB->value => __("module.role.options.guard_web"),
                    GuardEnum::API->value => __("module.role.options.guard_api"),
                ])->
                default(GuardEnum::API->value),

            "permissions" => Forms\Components\CheckboxList::make("permissions")->label(__("module.role.labels.permissions"))->validationAttribute(__("module.role.labels.permissions"))->
                relationship(titleAttribute: "name")->
                searchable()->
                bulkToggleable()->
                columns(1)->
                gridDirection("row")->
                options(function () {
                    return Permission::all()->pluck("name", "id")->toArray();
                })->
                descriptions(function () {
                    $descriptions = [];
                    foreach (Permission::all() as $permission) {
                        $descriptions[$permission->id] = __("module.permission.descriptions.{$permission->name}");
                    }
                    return $descriptions;
                }),

        ][$form];
    }
}
