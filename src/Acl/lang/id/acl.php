<?php

return [
    "role" => [
        "protected" => "Peran bawaan tidak dapat diubah atau dihapus.",
        "import" => [
            "started" => "Impor peran dimulai.",
            "completed" => "Impor peran selesai.",
            "failed" => "Impor peran gagal.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
                "permissions" => "permissions",
            ],
        ],
        "export" => [
            "started" => "Ekspor peran dimulai.",
            "completed" => "Ekspor peran selesai.",
            "failed" => "Ekspor peran gagal.",
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
        "protected" => "Izin bawaan tidak dapat diubah atau dihapus.",
        "import" => [
            "started" => "Impor izin dimulai.",
            "completed" => "Impor izin selesai.",
            "failed" => "Impor izin gagal.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
            ],
        ],
        "export" => [
            "started" => "Ekspor izin dimulai.",
            "completed" => "Ekspor izin selesai.",
            "failed" => "Ekspor izin gagal.",
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
