<?php

namespace Src\V0\User\Database\Seeders;

use App\Enums\Role;
use App\Enums\Permission;
use Tripteki\ACL\Contracts\Repository\Admin\IACLPermissionRepository as IACLPermissionAdminRepository;
use Tripteki\ACL\Contracts\Repository\Admin\IACLRoleRepository as IACLRoleAdminRepository;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * @var \Tripteki\ACL\Contracts\Repository\Admin\IACLPermissionRepository
     */
    protected $permissionAdminRepository;

    /**
     * @var \Tripteki\ACL\Contracts\Repository\Admin\IACLRoleRepository
     */
    protected $roleAdminRepository;

    /**
     * @param \Tripteki\ACL\Contracts\Repository\Admin\IACLPermissionRepository $permissionAdminRepository
     * @param \Tripteki\ACL\Contracts\Repository\Admin\IACLRoleRepository $roleAdminRepository
     * @return void
     */
    public function __construct(
        IACLPermissionAdminRepository $permissionAdminRepository,
        IACLRoleAdminRepository $roleAdminRepository)
    {
        $this->permissionAdminRepository = $permissionAdminRepository;
        $this->roleAdminRepository = $roleAdminRepository;
    }

    /**
     * @return void
     */
    public function run()
    {
        Artisan::call("adminer:generate:user", [ "--superuser" => true, ], $this->command->getOutput());

        $this->roleAdminRepository->rule((Role::ADMINISTRATOR)->value);
    }
}
