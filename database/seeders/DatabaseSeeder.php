<?php

namespace Database\Seeders;

use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Modules\Log\Database\Seeders\LogSeeder;
use Modules\Notification\Database\Seeders\NotificationSeeder;
use Illuminate\Database\Seeder;

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
            LogSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
