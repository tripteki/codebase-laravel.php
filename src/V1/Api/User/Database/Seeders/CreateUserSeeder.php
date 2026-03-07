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
     * @var string
     */
    public const DEFAULT_PASSWORD = "12345678";

    /**
     * @return void
     */
    public function run(): void
    {
        $name = UserEnum::SUPERUSER->value;
        $email = UserEnum::SUPERUSER->value . "@" . config("app.email_server");

        $user = User::firstOrCreate(
            [ "email" => $email, ],
            [
                "name" => $name,
                "password" => Hash::make(self::DEFAULT_PASSWORD),
                "email_verified_at" => now(),
            ]
        );

        $user->assignRole(RoleEnum::SUPERADMIN->value);
    }
}
