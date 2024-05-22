<?php

namespace App\Imports\Settings\Menus;

use Tripteki\SettingMenu\Scopes\MenuStrictScope;
use Tripteki\SettingMenu\Contracts\Repository\Admin\ISettingMenuDetailRepository;
use App\Http\Requests\Admin\Settings\Menus\MenuStoreValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class MenuImport implements ToCollection, WithStartRow
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
        $validator = (new MenuStoreValidation())->rules();

        Validator::make($rows->map(@function ($menu) {

            return [

                0 => @$menu[0],
                1 => MenuStrictScope::space(@$menu[0].".".@$menu[1]),
                2 => @$menu[2],
                3 => @$menu[3],
                4 => @$menu[4],
                5 => @$menu[5],
            ];

        })->toArray(), [

            "*.0" => $validator["bar"],
            "*.1" => $validator["menu"],
            "*.2" => $validator["category"],
            "*.3" => $validator["icon"],
            "*.4" => $validator["title"],
            "*.5" => $validator["description"],

        ])->validate();
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        $this->validate($rows);

        $menuAdminRepository = app(ISettingMenuDetailRepository::class);

        foreach ($rows as $row) {

            $menuAdminRepository->create($row[0], $row[1], [

                "category" => $row[2],
                "icon" => $row[3],
                "title" => $row[4],
                "description" => $row[5],
            ]);
        }
    }
};
