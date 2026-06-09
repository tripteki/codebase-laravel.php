<?php

use NotificationChannels\WebPush\PushSubscription;

return [

    /*
    |--------------------------------------------------------------------------
    | VAPID Authentication Keys
    |--------------------------------------------------------------------------
    |
    | Keys used to sign Web Push payloads. Generate with:
    | php artisan webpush:vapid
    |
    */

    "vapid" => [

        "subject" => env("VAPID_SUBJECT", env("APP_URL")),
        "public_key" => env("VAPID_PUBLIC_KEY"),
        "private_key" => env("VAPID_PRIVATE_KEY"),
        "pem_file" => env("VAPID_PEM_FILE"),
    ],

    /*
    |--------------------------------------------------------------------------
    | Push Subscription Model
    |--------------------------------------------------------------------------
    */

    "model" => PushSubscription::class,

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */

    "table_name" => env("WEBPUSH_DB_TABLE", "push_subscriptions"),

    "database_connection" => env("WEBPUSH_DB_CONNECTION", env("DB_CONNECTION")),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    |
    | Guzzle options passed to Minishlink\WebPush.
    |
    */

    "client_options" => [],

    /*
    |--------------------------------------------------------------------------
    | Payload Padding
    |--------------------------------------------------------------------------
    |
    | Automatic padding in bytes.
    |
    */

    "automatic_padding" => env("WEBPUSH_AUTOMATIC_PADDING", true),

    /*
    |--------------------------------------------------------------------------
    | Notification Queue
    |--------------------------------------------------------------------------
    |
    | Queue for Web Push notifications that implement ShouldQueue.
    | With QUEUE_CONNECTION=sync, jobs run immediately.
    |
    */

    "notification_queue" => env("WEBPUSH_NOTIFICATION_QUEUE", "notifications"),

];
