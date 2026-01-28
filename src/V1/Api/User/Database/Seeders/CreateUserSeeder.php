<?php

namespace Src\V1\Api\User\Database\Seeders;

use App\Models\User;
use Src\V1\Api\User\Enums\UserEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateUserSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $name = UserEnum::SUPERUSER->value;
        $email = UserEnum::SUPERUSER->value."@mail.com";
        $password = "12345678";

        $user = User::firstOrCreate(
            [ "email" => $email, ],
            [
                "name" => $name,
                "password" => Hash::make($password),
                "email_verified_at" => now(),
            ]
        );

        $user->assignRole(RoleEnum::SUPERADMIN->value);
    }
}
