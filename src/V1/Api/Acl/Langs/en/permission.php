<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permission Module Translations
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in the 'permission' module throughout 
    | the application. These include translations for widgets, headers, 
    | footers, tables, labels, etc.
    |
    */

    "permission" => [

        "navigation_group" => "Access Control",
        "navigation" => "Permissions",
        "label" => "Permission",

        "sections" => [

            "basic_info" => "Basic Information",
            "basic_info_description" => "Define the permission name and guard",
        ],

        "tables" => [

            "heading" => "Manage permissions",
            "description" => "List of all available permissions in the system.",
        ],

        "labels" => [

            "id" => "ID",
            "name" => "Name",
            "guard_name" => "Guard",
            "created_at" => "Creation",
            "updated_at" => "Updation",

            "new" => "Create permission",
            "import" => "Import permissions",
            "export" => "Export permissions",
            "mark_as_read" => "Mark as read",
            "download_csv" => "Download CSV",
            "download_xlsx" => "Download XLSX",
        ],

        "options" => [

            "guard_web" => "Web",
            "guard_api" => "API",
        ],

        "descriptions" => [

            // User permissions
            "user" => [
                "view" => "View users",
                "create" => "Create new users",
                "update" => "Update existing users",
                "delete" => "Delete users",
                "restore" => "Restore deleted users",
                "force-delete" => "Permanently delete users",

                // User imports
                "import" => [
                    "view" => "View user import history",
                    "create" => "Create new user imports",
                    "upload" => "Upload user import files",
                    "delete" => "Delete user imports",
                ],

                // User exports
                "export" => [
                    "view" => "View user export history",
                    "create" => "Create new user exports",
                    "download" => "Download user export files",
                    "delete" => "Delete user exports",
                ],
            ],

            // Role permissions
            "role" => [
                "view" => "View roles",
                "create" => "Create new roles",
                "update" => "Update existing roles",
                "delete" => "Delete roles",

                // Role imports
                "import" => [
                    "view" => "View role import history",
                    "create" => "Create new role imports",
                    "upload" => "Upload role import files",
                    "delete" => "Delete role imports",
                ],

                // Role exports
                "export" => [
                    "view" => "View role export history",
                    "create" => "Create new role exports",
                    "download" => "Download role export files",
                    "delete" => "Delete role exports",
                ],
            ],

            // Permission permissions
            "permission" => [
                "view" => "View permissions",
                "create" => "Create new permissions",
                "update" => "Update existing permissions",
                "delete" => "Delete permissions",

                // Permission imports
                "import" => [
                    "view" => "View permission import history",
                    "create" => "Create new permission imports",
                    "upload" => "Upload permission import files",
                    "delete" => "Delete permission imports",
                ],

                // Permission exports
                "export" => [
                    "view" => "View permission export history",
                    "create" => "Create new permission exports",
                    "download" => "Download permission export files",
                    "delete" => "Delete permission exports",
                ],
            ],
        ],

        "messages" => [

            "import_completed" => "Permission import completed successfully!",
            "import_body_notification" => "You can download the results from the flash notification that appeared.",
            "import_body_with_stats" => "Import completed: :successful successful, :failed failed rows.",
            "export_completed" => "Permission export completed successfully!",
            "export_body_notification" => "Download your file from the flash notification in CSV or XLSX format.",
            "export_body_with_download" => "Your export has completed with :count rows. Click below to download.",
        ],

    ],

];
