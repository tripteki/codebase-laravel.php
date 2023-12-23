<?php

$documentations = [

    "default" => [

        "api" => [ "title" => "Swagger", ],

        "routes" => [

            "api" => "api/docs",
        ],

        "paths" => [

            "use_absolute_path" => true,

            "docs_json" => "default.json",
            "docs_yaml" => "default.yaml",

            "format_to_use_for_docs" => env("SWAGGER_DEFAULT_FORMAT", "json"),

            "annotations" => [

                base_path("app"),
                base_path("src"),
            ],
        ],
    ],
];

return [

    "default" => env("SWAGGER", "default"),

    "documentations" => env("IS_SWAGGER", false) ? $documentations : [],

    "defaults" => [

        "generate_always" => true,
        "generate_yaml_copy" => false,

        "additional_config_url" => null,

        "operations_sort" => null,

        "validator_url" => null,

        "proxy" => false,

        "ui" => [

            "display" => [

                "filter" => true,

                "doc_expansion" => "none",
            ],

            "authorization" => [

                "persist_authorization" => false,

                "oauth2" => [

                    "use_pkce_with_authorization_code_grant" => false,
                ],
            ],
        ],

        "routes" => [

            "docs" => "/",

            "oauth2_callback" => "api/oauth2-callback",

            "middleware" => [

                "asset" => [],
                "api" => [],
                "docs" => [],
                "oauth2_callback" => [],
            ],

            "group_options" => [],
        ],

        "paths" => [

            "docs" => storage_path("swagger"),

            "views" => resource_path("views/vendor/swagger"),

            "base" => "api",

            "swagger_ui_assets_path" => "vendor/swagger-api/swagger-ui/dist",

            "excludes" => [],
        ],

        "scanOptions" => [

            "analyser" => null,
            "analysis" => null,

            "processors" => [],

            "pattern" => null,

            "exclude" => [],

            "open_api_spec_version" => \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION,
        ],

        "securityDefinitions" => [

            "securitySchemes" => [],

            "security" => [],
        ],

        "constants" => [

            "SWAGGER_PREFIX" => env("SWAGGER_PREFIX", "/api"),
        ],
    ],

];
