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
        ],

        "options" => [

            "guard_web" => "Web",
            "guard_api" => "API",
        ],

    ],

];
