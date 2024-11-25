<?php

namespace Database\Seeders;

use Src\V1\Api\User\Enums\UserEnum;
use App\Models\User as UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call("make:filament-user", [

            "--name" => ($name = UserEnum::SUPERUSER->value),
            "--email" => ($email = UserEnum::SUPERUSER->value."@mail.com"),
            "--password" => ($password = "12345678"),
        ]);

        UserModel::where("email", $email)->first()?->markEmailAsVerified();

        $this->command->getOutput();
    }
}
