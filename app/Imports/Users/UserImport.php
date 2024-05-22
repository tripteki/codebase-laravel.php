<?php

namespace App\Imports\Users;

use Tripteki\User\Contracts\Repository\Admin\IUserRepository as IUserAdminRepository;
use App\Http\Requests\Admin\Users\UserStoreValidation;
use App\Http\Requests\Admin\Users\UserUpdateValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class UserImport implements ToCollection, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @return void
     */
    protected function validate(Collection $rows)
    {
        $validator = (new UserStoreValidation())->rules();
        $validator["password"] = (new UserUpdateValidation())->rules()["password"];

        Validator::make($rows->toArray(), [

            "*.0" => $validator["name"],
            "*.1" => $validator["email"],
            "*.2" => $validator["password"],

        ])->validate();
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        $this->validate($rows);

        $userAdminRepository = app(IUserAdminRepository::class);

        foreach ($rows as $row) {

            $userAdminRepository->create([

                "name" => $row[0],
                "email" => $row[1],
                "password" => $row[2],
            ]);
        }
    }
};
