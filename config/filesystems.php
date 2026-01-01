<?php

return [

    "default" => env("FILESYSTEM_DISK", "private"),
    "cloud" => env("CLOUDSYSTEM_DISK", null),

    "disks" => [

        "private" => [

            "driver" => "local",
            "root" => storage_path("app/private"),
            "visibility" => "private",
            "throw" => false,
        ],

        "public" => [

            "driver" => "local",
            "root" => storage_path("app/public"),
            "url" => env("APP_URL", "http://localhost")."/storage",
            "visibility" => "public",
            "throw" => false,
        ],

        "s3" => [

            "driver" => "s3",
            "url" => env("AWS_URL"),
            "endpoint" => env("AWS_ENDPOINT"),
            "use_path_style_endpoint" => env("AWS_USE_PATH_STYLE_ENDPOINT", false),
            "key" => env("AWS_ACCESS_KEY_ID"),
            "secret" => env("AWS_SECRET_ACCESS_KEY"),
            "region" => env("AWS_DEFAULT_REGION"),
            "bucket" => env("AWS_BUCKET"),
            "throw" => false,
        ],

        "gcs" => [

            "driver" => "gcs",
            "path_prefix" => env("GCP_PATH", ""),
            "api_endpoint" => env("GCP_API_ENDPOINT", null),
            "storage_api_uri" => env("GCP_STORAGE_API_ENDPOINT", null),
            "key_file_path" => env("GCP_KEY_FILE_PATH", null),
            "key_file" => env("GCP_KEY_FILE", []),
            "project_id" => env("GCP_PROJECT_ID"),
            "bucket" => env("GCP_BUCKET"),
            "throw" => false,
        ],
    ],

    "links" => [

        public_path("storage") => storage_path("app/public"),
    ],

];
