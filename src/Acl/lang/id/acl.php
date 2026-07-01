<?php

return [
    "role" => [
        "name_exists" => "Nama role ini sudah dipakai",
        "name_reserved" => "Nama role ini sudah dikunci",
        "protected" => "Role bawaan tidak dapat diubah namanya atau dihapus",
        "not_found" => "Role tidak ditemukan",
        "permissions_not_found" => "Satu atau lebih permission tidak ditemukan",
        "in_use" => "Role ini masih dipakai oleh :count pengguna. Ubah role mereka dahulu.",
        "import" => [
            "started" => "Impor dimulai",
            "no_data" => "Tidak ada data untuk divalidasi",
            "missing_columns" => "Kolom yang diperlukan tidak ada: :columns",
            "completed" => "Impor selesai. :totalImported diimpor, :totalSkipped dilewati.",
            "failed" => "Impor gagal. Periksa format berkas lalu coba lagi.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
                "permissions" => "permissions",
            ],
        ],
        "export" => [
            "started" => "Ekspor dimulai",
            "completed" => "Ekspor selesai. Berkas siap diunduh.",
            "failed" => "Ekspor gagal. Coba lagi nanti.",
            "sheet_name" => "Peran",
            "column" => [
                "id" => "id",
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
                "permissions" => "permissions",
                "created_at" => "created_at",
                "updated_at" => "updated_at",
            ],
        ],
    ],
    "permission" => [
        "not_found" => "Permission tidak ditemukan",
        "name_exists" => "Nama permission ini sudah dipakai",
        "protected" => "Permission bawaan tidak dapat diubah namanya atau dihapus",
        "import" => [
            "started" => "Impor dimulai",
            "no_data" => "Tidak ada data untuk divalidasi",
            "missing_columns" => "Kolom yang diperlukan tidak ada: :columns",
            "completed" => "Impor selesai. :totalImported diimpor, :totalSkipped dilewati.",
            "failed" => "Impor gagal. Periksa format berkas lalu coba lagi.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
            ],
        ],
        "export" => [
            "started" => "Ekspor dimulai",
            "completed" => "Ekspor selesai. Berkas siap diunduh.",
            "failed" => "Ekspor gagal. Coba lagi nanti.",
            "sheet_name" => "Izin",
            "column" => [
                "id" => "id",
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
                "created_at" => "created_at",
                "updated_at" => "updated_at",
            ],
        ],
    ],
];
