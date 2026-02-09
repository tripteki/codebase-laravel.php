<?php

return [

    /**
     * The keys for auth (VAPID).
     * Must be safely stored and should not change.
     */
    'vapid' => [

        'subject' => env('VAPID_SUBJECT'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'pem_file' => env('VAPID_PEM_FILE'),
    ],

    /**
     * The Guzzle client options used by Minishlink\WebPush.
     */
    'client_options' => [],



    /**
     * The database connection that will be used by the migration and
     * the PushSubscription model shipped with this package.
     */
    'database_connection' => env('WEBPUSH_DB_CONNECTION', env('DB_CONNECTION')),

    /**
     * The name of the table that will be created by the migration and
     * used by the PushSubscription model shipped with this package.
     */
    'table_name' => env('WEBPUSH_DB_TABLE', 'push_subscriptions'),

    /**
     * The model that will be used to for push subscriptions.
     */
    'model' => \NotificationChannels\WebPush\PushSubscription::class,

];
