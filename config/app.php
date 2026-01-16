<?php

use Illuminate\Support\Facades\Facade;

return [

    "name" => env("APP_NAME", "Basecode"),
    "version" => env("APP_VERSION", @json_decode(file_get_contents(base_path("composer.json")), JSON_PRETTY_PRINT)["version"]),

    "url" => (function() {
        $url = env("APP_URL", "http://localhost");
        if ($url && !preg_match('/^https?:\/\//', $url)) {
            $url = "http://".$url;
        }
        return $url ?: "http://localhost";
    })(),

    "frontend_url" => env("FRONTEND_URL", "http://frontend.localhost"),

    "asset_url" => env("ASSET_URL", null),

    "env" => env("APP_ENV", "production"),
    "debug" => (bool) env("APP_DEBUG", false),

    "timezone" => env("APP_TIMEZONE", "UTC"),
    "locale" => env("APP_LOCALE", "en"),
    "fallback_locale" => env("APP_FALLBACK_LOCALE", "en"),
    "faker_locale" => env("APP_FAKER_LOCALE", "en_US"),

    "key" => env("APP_KEY"),
    "cipher" => "AES-256-CBC",
    "previous_keys" => [ ...array_filter(explode(",", env("APP_PREVIOUS_KEYS", ""))), ],

    /*
    | Supported: "file", "cache".
    */
    "maintenance" => [

        "driver" => env("APP_MAINTENANCE_DRIVER", "file"),
        "store" => "database",
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    "providers" => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\SecurityServiceProvider::class,

        /*
         * Module Service Providers...
         */
        Src\V1\Web\Providers\Filament\AdminPanelProvider::class,
        Src\V1\Web\Providers\Filament\FilamentServiceProvider::class,

        Src\V1\Api\Auth\Providers\AuthServiceProvider::class,
        Src\V1\Api\User\Providers\UserServiceProvider::class,
        Src\V1\Api\Acl\Providers\AclServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    "aliases" => [

        //
    ],

];
