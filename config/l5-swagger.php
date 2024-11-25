<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Configuration
    |--------------------------------------------------------------------------
    |
    | The default configuration that determines which Swagger setup to use. 
    | This will be loaded based on the `SWAGGER` environment variable.
    |
    */
    "default" => env("SWAGGER", "default"),

    /*
    |--------------------------------------------------------------------------
    | Documentations Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the documentation settings for your API. 
    | The `documentations` key is set to only be active if the environment is 
    | not `production` (useful for enabling/disabling Swagger UI in production).
    |
    */
    "documentations" => config("app.env") !== "production" ? [

        "default" => [

            "api" => [

                "title" => "Swagger",
            ],

            "routes" => [

                "api" => "api/docs",
            ],

            "paths" => [

                "use_absolute_path" => true,
                "docs_yaml" => "api-docs.yaml",
                "docs_json" => "api-docs.json",
                "format_to_use_for_docs" => env("SWAGGER_DEFAULT_FORMAT", "json"),

                "annotations" => [

                    base_path("src"),
                ],
            ],
        ],

    ] : [],

    /*
    |--------------------------------------------------------------------------
    | Default Swagger Settings
    |--------------------------------------------------------------------------
    |
    | These are the default settings for generating Swagger documentation. 
    | These settings control how often the docs are generated, whether to 
    | use a proxy, and other configuration options.
    |
    */
    "defaults" => [

        "generate_always" => env("SWAGGER_AUTO_GENERATE", true),
        "generate_yaml_copy" => true,
        "proxy" => false,
        "additional_config_url" => null,
        "validator_url" => null,

        "constants" => [

            //
        ],

        "securityDefinitions" => [

            "securitySchemes" => [

                //
            ],

            "security" => [

                //
            ],
        ],

        //

        "operations_sort" => null,

        "ui" => [

            "display" => [

                "dark_mode" => env("SWAGGER_THEME_DARK_MODE", false),
                "doc_expansion" => "none",
                "filter" => true,
            ],

            "authorization" => [

                "persist_authorization" => false,
                "oauth2" => [ "use_pkce_with_authorization_code_grant" => false, ],
            ],
        ],

        "routes" => [

            "docs" => "docs",

            "oauth2_callback" => "api/oauth2-callback",

            "middleware" => [

                "asset" => [

                    //
                ],

                "docs" => [

                    //
                ],

                "oauth2_callback" => [

                    //
                ],

                "api" => [

                    //
                ],
            ],

            "group_options" => [

                //
            ],
        ],

        "paths" => [

            "base" => null,
            "docs" => storage_path("swagger"),
            "views" => base_path("vendor/darkaonline/l5-swagger/resources/views"),
            "swagger_ui_assets_path" => "vendor/swagger-api/swagger-ui/dist",

            "excludes" => [

                //
            ],
        ],

        "scanOptions" => [

            "analyser" => null,
            "analysis" => null,
            "pattern" => null,
            "open_api_spec_version" => \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION,

            "default_processors_configuration" => [

                //
            ],

            "processors" => [

                //
            ],

            "exclude" => [

                //
            ],
        ],
    ],

];
