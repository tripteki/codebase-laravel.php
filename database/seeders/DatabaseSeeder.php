<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\Log\Database\Seeders\LogSeeder;
use Modules\Notification\Database\Seeders\NotificationSeeder;
use Modules\User\Database\Seeders\SettingSeeder;
use Modules\User\Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $this->call([

            AclSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            LogSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
