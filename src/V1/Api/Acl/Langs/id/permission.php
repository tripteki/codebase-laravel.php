<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permission Module Translations (Indonesian)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in the 'permission' module throughout 
    | the application. These include translations for widgets, headers, 
    | footers, tables, labels, etc.
    |
    */

    "permission" => [

        "navigation_group" => "Kontrol Akses",
        "navigation" => "Permission",
        "label" => "Permission",

        "sections" => [

            "basic_info" => "Informasi Dasar",
            "basic_info_description" => "Tentukan nama permission dan guard",
        ],

        "tables" => [

            "heading" => "Kelola permission",
            "description" => "Daftar semua permission yang tersedia dalam sistem.",
        ],

        "labels" => [

            "id" => "ID",
            "name" => "Nama",
            "guard_name" => "Guard",
            "created_at" => "Dibuat",
            "updated_at" => "Diperbarui",

            "new" => "Buat permission",
            "import" => "Impor permission",
            "export" => "Ekspor permission",
            "mark_as_read" => "Tandai dibaca",
            "download_csv" => "Unduh CSV",
            "download_xlsx" => "Unduh XLSX",
        ],

        "options" => [

            "guard_web" => "Web",
            "guard_api" => "API",
        ],

        "descriptions" => [

            // User permissions
            "user" => [
                "view" => "Lihat pengguna",
                "create" => "Buat pengguna baru",
                "update" => "Perbarui pengguna yang ada",
                "delete" => "Hapus pengguna",
                "restore" => "Pulihkan pengguna yang dihapus",
                "force-delete" => "Hapus permanen pengguna",

                // User imports
                "import" => [
                    "view" => "Lihat riwayat impor pengguna",
                    "create" => "Buat impor pengguna baru",
                    "upload" => "Unggah file impor pengguna",
                    "delete" => "Hapus impor pengguna",
                ],

                // User exports
                "export" => [
                    "view" => "Lihat riwayat ekspor pengguna",
                    "create" => "Buat ekspor pengguna baru",
                    "download" => "Unduh file ekspor pengguna",
                    "delete" => "Hapus ekspor pengguna",
                ],
            ],

            // Role permissions
            "role" => [
                "view" => "Lihat role",
                "create" => "Buat role baru",
                "update" => "Perbarui role yang ada",
                "delete" => "Hapus role",

                // Role imports
                "import" => [
                    "view" => "Lihat riwayat impor role",
                    "create" => "Buat impor role baru",
                    "upload" => "Unggah file impor role",
                    "delete" => "Hapus impor role",
                ],

                // Role exports
                "export" => [
                    "view" => "Lihat riwayat ekspor role",
                    "create" => "Buat ekspor role baru",
                    "download" => "Unduh file ekspor role",
                    "delete" => "Hapus ekspor role",
                ],
            ],

            // Permission permissions
            "permission" => [
                "view" => "Lihat permission",
                "create" => "Buat permission baru",
                "update" => "Perbarui permission yang ada",
                "delete" => "Hapus permission",

                // Permission imports
                "import" => [
                    "view" => "Lihat riwayat impor permission",
                    "create" => "Buat impor permission baru",
                    "upload" => "Unggah file impor permission",
                    "delete" => "Hapus impor permission",
                ],

                // Permission exports
                "export" => [
                    "view" => "Lihat riwayat ekspor permission",
                    "create" => "Buat ekspor permission baru",
                    "download" => "Unduh file ekspor permission",
                    "delete" => "Hapus ekspor permission",
                ],
            ],
        ],

        "messages" => [

            "import_completed" => "Impor permission berhasil diselesaikan!",
            "import_body_notification" => "Anda dapat mengunduh hasil dari notifikasi flash yang muncul.",
            "import_body_with_stats" => "Impor selesai: :successful berhasil, :failed baris gagal.",
            "export_completed" => "Ekspor permission berhasil diselesaikan!",
            "export_body_notification" => "Unduh file Anda dari notifikasi flash dalam format CSV atau XLSX.",
            "export_body_with_download" => "Ekspor Anda telah selesai dengan :count baris. Klik di bawah untuk mengunduh.",
        ],

    ],

];
