<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Module Translations
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in the 'user' module throughout 
    | the application. These include translations for widgets, headers, 
    | footers, tables, labels, etc.
    |
    */

    "user" => [

        "navigation_group" => "Manajemen Pengguna",
        "navigation" => "Pengguna",
        "label" => "Pengguna",

        "headers" => [

            //
        ],

        "footers" => [

            //
        ],

        "widgets" => [

            "filters" => [

                "week" => "Minggu",
                "month" => "Bulan",
            ],

            "labels" => [

                "activates" => "Pengguna Aktif",
                "deactivates" => "Pengguna Tidak Aktif",
            ],

            "stats" => [

                "total" => "Total Pengguna",
                "total_description" => "Semua pengguna terdaftar",
                "active" => "Pengguna Aktif",
                "active_description" => "Email terverifikasi",
                "inactive" => "Pengguna Tidak Aktif",
                "inactive_description" => "Akun dinonaktifkan",
                "new_today" => "Pengguna Baru Hari Ini",
                "new_today_description" => "Terdaftar hari ini",
            ],
        ],

        "tables" => [

            "heading" => "Pengelolaan pengguna",
            "description" => "Daftar kontrol untuk mengelola akun pengguna, mengubah pengaturan, dan mengatur akses.",
        ],

        "forms" => [

            //
        ],

        "buttons" => [

            //
        ],

        "steps" => [

            "information" => "Informasi",
            "credential" => "Kredensial",
        ],

        "labels" => [

            "id" => "ID",
            "name" => "Nama",
            "email" => "E-Mail",
            "password" => "Password",
            "password_confirmation" => "Konfirmasi Password",
            "email_verified_at" => "Verifikasi",
            "created_at" => "Pembuatan",
            "updated_at" => "Pembaruan",
            "deleted_at" => "Penghapusan",

            "new" => "Buat pengguna",
            "import" => "Impor pengguna",
            "export" => "Ekspor pengguna",
        ],

        "api" => [

            "account_activated" => [

                "greeting" => "Halo :name!",
                "subject" => "Akun Anda Telah Diaktifkan",
                "line" => "Kami dengan senang hati memberitahukan Anda bahwa akun Anda telah berhasil diaktifkan.",
                "thank_you" => "Terima kasih telah menjadi bagian dari komunitas kami!",
            ],

            "account_deactivated" => [

                "greeting" => "Halo :name!",
                "subject" => "Akun Anda Telah Dinonaktifkan",
                "line" => "Kami menyesal memberitahukan Anda bahwa akun Anda telah dinonaktifkan. Jika Anda merasa ini adalah kesalahan, harap hubungi dukungan.",
                "thank_you" => "Kami berharap dapat menyelesaikan masalah ini sesegera mungkin.",
            ],
        ],

    ],

];
