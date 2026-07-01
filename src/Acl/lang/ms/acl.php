<?php

return [
    "role" => [
        "protected" => "Peranan terbina dalam tidak boleh diubah suai atau dipadam.",
        "import" => [
            "started" => "Import peranan dimulakan.",
            "completed" => "Import peranan selesai.",
            "failed" => "Import peranan gagal.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
                "permissions" => "permissions",
            ],
        ],
        "export" => [
            "started" => "Eksport peranan dimulakan.",
            "completed" => "Eksport peranan selesai.",
            "failed" => "Eksport peranan gagal.",
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
        "protected" => "Kebenaran terbina dalam tidak boleh diubah suai atau dipadam.",
        "import" => [
            "started" => "Import kebenaran dimulakan.",
            "completed" => "Import kebenaran selesai.",
            "failed" => "Import kebenaran gagal.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
            ],
        ],
        "export" => [
            "started" => "Eksport kebenaran dimulakan.",
            "completed" => "Eksport kebenaran selesai.",
            "failed" => "Eksport kebenaran gagal.",
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
