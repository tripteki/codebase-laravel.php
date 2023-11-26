<?php

return [

    "default" => env("FILESYSTEM_DISK", "private"),
    "cloud" => env("CLOUDSYSTEM_DISK", null),

    "disks" => [

        "private" => [

            "driver" => "local",
            "root" => storage_path("disks/private"),
            "visibility" => "private",
            "throw" => false,
        ],

        "public" => [

            "driver" => "local",
            "root" => storage_path("disks/public"),
            "url" => env("APP_URL")."/storage",
            "visibility" => "public",
            "throw" => false,
        ],
    ],

    "links" => [

        public_path("storage") => storage_path("disks/public"),
    ],

];
