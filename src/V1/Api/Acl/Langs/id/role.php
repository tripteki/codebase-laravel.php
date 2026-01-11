<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Role Module Translations (Indonesian)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in the 'role' module throughout 
    | the application. These include translations for widgets, headers, footers, 
    | tables, labels, etc.
    |
    */

    "role" => [

        "navigation_group" => "Kontrol Akses",
        "navigation" => "Role",
        "label" => "Role",

        "sections" => [

            "basic_info" => "Informasi Dasar",
            "basic_info_description" => "Tentukan nama role dan guard",
            "permissions" => "Permission",
            "permissions_description" => "Tetapkan permission untuk role ini",
        ],

        "tables" => [

            "heading" => "Kelola role",
            "description" => "Daftar role dengan permission dan kontrol akses terkait.",
        ],

        "labels" => [

            "id" => "ID",
            "name" => "Nama",
            "guard_name" => "Guard",
            "permissions" => "Permission",
            "permissions_count" => "Permission",
            "created_at" => "Dibuat",
            "updated_at" => "Diperbarui",

            "new" => "Buat role",
            "import" => "Impor role",
            "export" => "Ekspor role",
            "mark_as_read" => "Tandai dibaca",
            "download_csv" => "Unduh CSV",
            "download_xlsx" => "Unduh XLSX",
        ],

        "options" => [

            "guard_web" => "Web",
            "guard_api" => "API",
        ],

        "messages" => [

            "import_completed" => "Impor role berhasil diselesaikan!",
            "import_body_notification" => "Anda dapat mengunduh hasil dari notifikasi flash yang muncul.",
            "import_body_with_stats" => "Impor selesai: :successful berhasil, :failed baris gagal.",
            "export_completed" => "Ekspor role berhasil diselesaikan!",
            "export_body_notification" => "Unduh file Anda dari notifikasi flash dalam format CSV atau XLSX.",
            "export_body_with_download" => "Ekspor Anda telah selesai dengan :count baris. Klik di bawah untuk mengunduh.",
        ],

    ],

];
