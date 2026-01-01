<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Module Translations
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in the 'user' module throughout 
    | the application. These include translations for widgets, headers, 
    | footers, tables, labels, etc.
    |
    */

    "user" => [

        "navigation_group" => "User Management",
        "navigation" => "User",
        "label" => "User",

        "headers" => [

            //
        ],

        "footers" => [

            //
        ],

        "widgets" => [

            "filters" => [

                "week" => "Week",
                "month" => "Month",
            ],

            "labels" => [

                "activates" => "User Activates",
                "deactivates" => "User Deactivates",
            ],

            "stats" => [

                "total" => "Total Users",
                "total_description" => "All registered users",
                "active" => "Active Users",
                "active_description" => "Verified email",
                "inactive" => "Inactive Users",
                "inactive_description" => "Deactivated accounts",
                "new_today" => "New Users Today",
                "new_today_description" => "Registered today",
            ],
        ],

        "tables" => [

            "heading" => "Manage user",
            "description" => "List of controls to manage user accounts, modify settings, and adjust access.",
        ],

        "forms" => [

            //
        ],

        "buttons" => [

            //
        ],

        "steps" => [

            "information" => "Information",
            "credential" => "Credential",
        ],

        "labels" => [

            "id" => "ID",
            "name" => "Name",
            "email" => "E-Mail",
            "password" => "Password",
            "password_confirmation" => "Password Confirmation",
            "email_verified_at" => "Verification",
            "created_at" => "Creation",
            "updated_at" => "Updation",
            "deleted_at" => "Deletion",

            "new" => "Create user",
            "import" => "Import users",
            "export" => "Export users",
        ],

        "api" => [

            "account_activated" => [

                "greeting" => "Hello :name!",
                "subject" => "Your Account Has Been Activated",
                "line" => "We are excited to let you know that your account has been successfully activated.",
                "thank_you" => "Thank you for being a part of our community!",
            ],

            "account_deactivated" => [

                "greeting" => "Hello :name!",
                "subject" => "Your Account Has Been Deactivated",
                "line" => "We regret to inform you that your account has been deactivated. If you think this is a mistake, please contact support.",
                "thank_you" => "We hope to resolve this issue as soon as possible.",
            ],
        ],

    ],

];
