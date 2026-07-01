<?php

return [
    "role" => [
        "name_exists" => "Nama peranan ini sudah digunakan",
        "name_reserved" => "Nama peranan ini sudah dikhaskan",
        "protected" => "Peranan bawaan tidak boleh ditukar nama atau dipadam",
        "not_found" => "Peranan tidak dijumpai",
        "permissions_not_found" => "Satu atau lebih kebenaran tidak dijumpai",
        "in_use" => "Peranan ini masih digunakan oleh :count pengguna. Tukar peranan mereka dahulu.",
        "import" => [
            "started" => "Import dimulakan",
            "no_data" => "Tiada data untuk disemak",
            "missing_columns" => "Lajur yang diperlukan tiada: :columns",
            "completed" => "Import selesai. :totalImported diimport, :totalSkipped dilangkau.",
            "failed" => "Import gagal. Semak format fail dan cuba lagi.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
                "permissions" => "permissions",
            ],
        ],
        "export" => [
            "started" => "Eksport dimulakan",
            "completed" => "Eksport selesai. Fail sedia untuk dimuat turun.",
            "failed" => "Eksport gagal. Sila cuba lagi nanti.",
            "sheet_name" => "Peranan",
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
        "not_found" => "Kebenaran tidak dijumpai",
        "name_exists" => "Nama kebenaran ini sudah digunakan",
        "protected" => "Kebenaran bawaan tidak boleh ditukar nama atau dipadam",
        "import" => [
            "started" => "Import dimulakan",
            "no_data" => "Tiada data untuk disemak",
            "missing_columns" => "Lajur yang diperlukan tiada: :columns",
            "completed" => "Import selesai. :totalImported diimport, :totalSkipped dilangkau.",
            "failed" => "Import gagal. Semak format fail dan cuba lagi.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
            ],
        ],
        "export" => [
            "started" => "Eksport dimulakan",
            "completed" => "Eksport selesai. Fail sedia untuk dimuat turun.",
            "failed" => "Eksport gagal. Sila cuba lagi nanti.",
            "sheet_name" => "Kebenaran",
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
