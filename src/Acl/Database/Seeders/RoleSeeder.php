<?php

namespace Modules\Acl\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;

        $superadmin = Role::firstOrCreate(["name" => RoleEnum::SUPERADMIN->value, "guard_name" => $guard]);
        $admin = Role::firstOrCreate(["name" => RoleEnum::ADMIN->value, "guard_name" => $guard]);
        $speaker = Role::firstOrCreate(["name" => RoleEnum::SPEAKER->value, "guard_name" => $guard]);
        $delegate = Role::firstOrCreate(["name" => RoleEnum::DELEGATE->value, "guard_name" => $guard]);
        $exhibitor = Role::firstOrCreate(["name" => RoleEnum::EXHIBITOR->value, "guard_name" => $guard]);
        $sponsor = Role::firstOrCreate(["name" => RoleEnum::SPONSOR->value, "guard_name" => $guard]);
        $visitor = Role::firstOrCreate(["name" => RoleEnum::VISITOR->value, "guard_name" => $guard]);
    }
}
