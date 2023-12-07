<?php

return [

    "state_file" => storage_path("logs/supervisor.json"),

    "server" => [

        "command:development" => [

            "octane:start",
            "--host=".env("SERVER_HOST", "0.0.0.0"),
            "--watch",
            "--workers=auto",
            "--task-workers=auto",
            "--no-interaction",
            "--no-ansi",
            "--verbose",
        ],

        "command:production" => [

            "octane:start",
            "--host=".env("SERVER_HOST", "0.0.0.0"),
            "--workers=auto",
            "--task-workers=auto",
            "--no-interaction",
            "--no-ansi",
            "--verbose",
        ],

        "process" => 1,
        "increment" => ["SERVER_PORT" => env("SERVER_PORT", "8000")],
        "stdout" => storage_path("logs/supervisor-server-stdout.log"),
        "stderr" => storage_path("logs/supervisor-server-stderr.log"),
    ],

];
