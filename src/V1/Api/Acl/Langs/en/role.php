<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Role Module Translations
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in the 'role' module throughout 
    | the application. These include translations for widgets, headers, footers, 
    | tables, labels, etc.
    |
    */

    "role" => [

        "navigation_group" => "Access Control",
        "navigation" => "Roles",
        "label" => "Role",

        "sections" => [

            "basic_info" => "Basic Information",
            "basic_info_description" => "Define the role name and guard",
            "permissions" => "Permissions",
            "permissions_description" => "Assign permissions to this role",
        ],

        "tables" => [

            "heading" => "Manage roles",
            "description" => "List of roles with their associated permissions and access controls.",
        ],

        "labels" => [

            "id" => "ID",
            "name" => "Name",
            "guard_name" => "Guard",
            "permissions" => "Permissions",
            "permissions_count" => "Permissions",
            "created_at" => "Creation",
            "updated_at" => "Updation",

            "new" => "Create role",
            "import" => "Import roles",
            "export" => "Export roles",
            "mark_as_read" => "Mark as read",
            "download_csv" => "Download CSV",
            "download_xlsx" => "Download XLSX",
        ],

        "options" => [

            "guard_web" => "Web",
            "guard_api" => "API",
        ],

        "messages" => [

            "import_completed" => "Role import completed successfully!",
            "import_body_notification" => "You can download the results from the flash notification that appeared.",
            "import_body_with_stats" => "Import completed: :successful successful, :failed failed rows.",
            "export_completed" => "Role export completed successfully!",
            "export_body_notification" => "Download your file from the flash notification in CSV or XLSX format.",
            "export_body_with_download" => "Your export has completed with :count rows. Click below to download.",
        ],

    ],

];
