<?php

namespace Modules\User\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;
use Modules\User\App\Support\UserDefaultsHelper;

class CreateTenantAdminUserSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $tenantId = current_tenant_id();

        if ($tenantId === null) {
            return;
        }

        sync_permissions_team_context();

        $email = RoleEnum::ADMIN->value.".".$tenantId."@".config("app.email_server");

        $user = User::firstOrCreate(
            [
                "email" => $email,
                "tenant_id" => $tenantId,
            ],
            [
                "name" => RoleEnum::ADMIN->value,
                "password" => UserDefaultsHelper::defaultPassword(),
                "email_verified_at" => now(),
            ],
        );

        if ($user->email_verified_at === null) {
            $user->forceFill(["email_verified_at" => now()])->save();
        }

        $role = Role::findByName(RoleEnum::ADMIN->value, GuardEnum::WEB->value);

        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }
    }
}
