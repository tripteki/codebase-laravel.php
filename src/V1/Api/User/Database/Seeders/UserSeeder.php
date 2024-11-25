<?php

namespace Src\V1\Api\User\Database\Seeders;

use App\Models\User;
use Src\V1\Api\User\Enums\UserEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        Artisan::call("make:filament-user", [

            "--name" => ($name = UserEnum::SUPERUSER->value),
            "--email" => ($email = UserEnum::SUPERUSER->value."@mail.com"),
            "--password" => ($password = "12345678"),
        ]);

        User::where("email", $email)->first()?->markEmailAsVerified();

        $this->command->getOutput();
    }
}
