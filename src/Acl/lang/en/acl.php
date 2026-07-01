<?php

return [
    "role" => [
        "protected" => "Built-in roles cannot be modified or deleted.",
        "import" => [
            "started" => "Role import started.",
            "completed" => "Role import completed.",
            "failed" => "Role import failed.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
                "permissions" => "permissions",
            ],
        ],
        "export" => [
            "started" => "Role export started.",
            "completed" => "Role export completed.",
            "failed" => "Role export failed.",
            "sheet_name" => "Roles",
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
        "protected" => "Built-in permissions cannot be modified or deleted.",
        "import" => [
            "started" => "Permission import started.",
            "completed" => "Permission import completed.",
            "failed" => "Permission import failed.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
            ],
        ],
        "export" => [
            "started" => "Permission export started.",
            "completed" => "Permission export completed.",
            "failed" => "Permission export failed.",
            "sheet_name" => "Permissions",
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
