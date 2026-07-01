<?php

return [
    "role" => [
        "name_exists" => "This role name is already used",
        "name_reserved" => "This role name is reserved",
        "protected" => "Built-in roles cannot be renamed or deleted",
        "not_found" => "Role not found",
        "permissions_not_found" => "One or more permissions were not found",
        "in_use" => "This role is still assigned to :count users. Change their role first.",
        "import" => [
            "started" => "Import started",
            "no_data" => "No data to validate",
            "missing_columns" => "Missing required columns: :columns",
            "completed" => "Import completed. :totalImported imported, :totalSkipped skipped.",
            "failed" => "Import failed. Check the file format and try again.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
                "permissions" => "permissions",
            ],
        ],
        "export" => [
            "started" => "Export started",
            "completed" => "Export completed. The file is ready to download.",
            "failed" => "Export failed. Please try again later.",
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
        "not_found" => "Permission not found",
        "name_exists" => "This permission name is already used",
        "protected" => "Built-in permissions cannot be renamed or deleted",
        "import" => [
            "started" => "Import started",
            "no_data" => "No data to validate",
            "missing_columns" => "Missing required columns: :columns",
            "completed" => "Import completed. :totalImported imported, :totalSkipped skipped.",
            "failed" => "Import failed. Check the file format and try again.",
            "column" => [
                "tenant" => "tenant",
                "name" => "name",
                "guard_name" => "guard_name",
            ],
        ],
        "export" => [
            "started" => "Export started",
            "completed" => "Export completed. The file is ready to download.",
            "failed" => "Export failed. Please try again later.",
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
