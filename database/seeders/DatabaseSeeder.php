<?php

namespace Database\Seeders;

use Src\V1\Api\Acl\Database\Seeders\AclSeeder;
use Src\V1\Api\User\Database\Seeders\UserSeeder;
use Src\V1\Api\Log\Database\Seeders\LogSeeder;
use Database\Seeders\Tenant\TenantSeeder;
use Database\Seeders\Stage\StageSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $seeders = [
            AclSeeder::class,
            UserSeeder::class,
            LogSeeder::class,
            StageSeeder::class,
        ];

        if (config("tenancy.is_tenancy")) {
            $seeders[] = TenantSeeder::class;
        }

        $seeders[] = SettingSeeder::class;

        $this->call($seeders);
    }
}
