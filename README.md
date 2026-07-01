<h1 align="center">Codebase Laravel</h1>

Backend skeleton built with Laravel as a modular monolith. It applies event-listener driven development, queue processing, realtime broadcasting, scheduled tasks, and caching, with RESTful design, SOLID, dependency injection, the repository pattern, and DTOs.

It shares one REST contract and the same realtime events with Codebase FastAPI and Codebase NestJS, so Codebase Nuxt and Codebase Next work with any of them.

## Conventions

| Topic | Rule |
|-------|------|
| Brand | Codebase. Primary `#2563eb`, secondary `#84cc16`, tertiary `#1e3a8a` |
| Roles | `superadmin`, `admin`, `user` |
| Format | Indent 4, double quotes, trailing commas, no space before `(` (Pint `laravel` preset). Keep PHPDoc with fully qualified class names |
| Layers | Controller, service, repository. Spatie Data DTO in, transformer DTO out. Events and listeners for side effects |
| Validation | HTTP 422 `{ "detail": [{ "type", "loc", "msg", "input", "ctx" }] }` |
| Errors | `{ "detail": "..." }` |
| Realtime | Reverb + Echo on private channel `user.{id}` |
| Tenancy | `IS_TENANCY=false` for the shared contract. `true` adds the `/{tenant}` routes |
| Locales | `en`, `id`, `ms` |
| Rate limits | Production only |

Cursor rules live in `.cursor/rules/`.

## Features

| Feature | Technology |
|---------|------------|
| REST API with Swagger | Laravel 13 + L5-Swagger |
| JWT access and refresh tokens | tymon/jwt-auth |
| Roles and permissions | Spatie Permission |
| Activity log | Spatie Activitylog |
| Realtime | Laravel Reverb + Echo |
| Queue and scheduler | Laravel Queue + Scheduler |
| Import and export (CSV, XLS, XLSX) | maatwebsite/excel |
| Notifications and Web Push | Laravel Notifications + webpush |
| Email | Blade mail templates |
| Multi tenancy | stancl/tenancy |
| Modules | nwidart/laravel-modules |
| Internationalization | `en`, `id`, `ms` |

## Getting Started

### Requirements

- PHP 8.3 (`.php-version` pins `8.3.13`)
- Composer 2.7+
- MySQL 8+, PostgreSQL 14+, or SQLite
- Redis for queue, cache, and broadcast in production

### Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan db:seed
php artisan storage:link
```

The seeder creates roles, permissions, brand settings, and `superuser@{APP_EMAIL_SERVER}` with password `12345678`.

### Configuration

| Variable | Description |
|----------|-------------|
| `IS_TENANCY` | `false` for the shared contract |
| `APP_URL`, `FRONTEND_URL` | API URL and base URL for signed links in emails |
| `CORS_ORIGIN` | Allowed origins as a JSON array or comma list. `FRONTEND_URL` when empty |
| `DB_*` | Database connection |
| `QUEUE_CONNECTION` | `sync` runs jobs in the request, `redis` or `database` needs a worker |
| `MAIL_DRIVER` | `smtp`, or `log` to write emails to `storage/logs/log.log` |
| `BROADCAST_CONNECTION` | `reverb`, or `log` for debugging |
| `REVERB_*` | Reverb server and app keys |
| `VAPID_*` | Optional Web Push keys |

### Run

| Command | Description |
|---------|-------------|
| `composer dev` | Octane with file watching on `http://localhost:8000` |
| `php artisan reverb:start` | Realtime server |
| `php artisan queue:work --queue=user-admin-queue,acl-admin-queue,event-admin-queue,notifications,default` | Queue worker |
| `php artisan schedule:work` | Scheduler |
| `composer build` and `composer start` | Production |

The worker must listen to every queue:

| Queue | Jobs |
|-------|------|
| `user-admin-queue` | User import and export |
| `acl-admin-queue` | Role and permission import and export |
| `event-admin-queue` | Event import and export |
| `notifications` | Web Push |
| `default` | Queued broadcasts |

Swagger UI: `http://localhost:8000/api/docs`

## Docker

The image is `php:8.3-cli-alpine` with Swoole, Redis, and MySQL extensions. One image runs every Laravel process.

```bash
cp .env.example .env
docker compose build
docker compose run --rm --no-deps app php artisan key:generate --show
docker compose run --rm --no-deps app php artisan jwt:secret --show
docker compose up -d
```

Put the generated keys in `APP_KEY` and `JWT_SECRET` before `docker compose up`. Migrations and seeders run manually:

```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

| Service | Purpose | Port |
|---------|---------|------|
| `app` | Octane (Swoole) HTTP server | `OCTANE_PORT` (8000) |
| `reverb` | Reverb WebSocket server | `REVERB_PUBLIC_PORT` (8080) |
| `queue` | Worker for every queue in `QUEUE_NAMES` | — |
| `scheduler` | `schedule:work` | — |
| `mysql`, `redis` | MySQL 8.4 and Redis 7 | internal |

Compose points the database, Redis, cache, queue, and Reverb at its own containers. Change their credentials with `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_ROOT_PASSWORD`, and `REDIS_PASSWORD`. Uploaded files live in the `laravel-storage` volume.

## API

All routes live under `/api/v1`.

| Area | Routes |
|------|--------|
| Auth | `/auth/login`, `/auth/register`, `/auth/refresh`, `/auth/logout`, `/auth/forgot-password`, `/auth/reset-password/{email}`, `/auth/verify-email/{email}` |
| Profile | `/users/me`, `/users/me/accesses`, `/users/me/interests` |
| Notifications | `/notifications/*` |
| Web Push | `/webpush/subscribe`, `/webpush/unsubscribe` |
| Admin | `/admin/users`, `/admin/roles`, `/admin/permissions`, `/admin/activities`, `/admin/notifications`, `/admin/settings`, `/admin/dashboard`, `/admin/search` |
| Public | `/settings/variables` |

Login body is `{ "identifierKey": "email" | "name", "identifierValue": "...", "password": "..." }` and returns `{ accessToken, refreshToken, accessTokenTtl, refreshTokenTtl }`. `identifierKey` is optional; without it, a value containing `@` is treated as an email, otherwise as a name.

Lists accept `limitPage`, `currentPage`, `orders=field:asc`, and `filters=key:value` and return `{ total, totalPage, perPage, currentPage, nextPage, previousPage, firstPage, lastPage, data }`.

Export files are stored on the public disk under `export/` and returned as `fileUrl: "storage/export/{filename}"`.

## Realtime

Events go to private channel `user.{id}`. Echo authorizes the channel with `POST /broadcasting/auth` and the access token.

| Event | Payload |
|-------|---------|
| `v1.notification.created` | `id`, `unread` |
| `v1.admin.refresh` | `userId`, `scopes` |
| `v1.user.admin.imported` | `userId`, `filename`, `totalImported`, `totalSkipped` |
| `v1.user.admin.imported-failed` | `userId`, `filename`, `error` |
| `v1.user.admin.exported` | `userId`, `filename`, `fileUrl` |
| `v1.user.admin.exported-failed` | `userId`, `error` |
| `v1.acl.role.admin.*`, `v1.acl.permission.admin.*` | Same as the user import and export events |
| `v1.user.admin.activated`, `v1.user.admin.deactivated` | `id`, `name`, `email`, `email_verified_at`, `created_at`, `updated_at`, `deleted_at` |

Import and export events go to the admin who started the job. Activate and deactivate events go to the affected user.

```javascript
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: "reverb",
    key: reverbAppKey,
    wsHost: "127.0.0.1",
    wsPort: 8080,
    forceTLS: false,
    enabledTransports: ["ws", "wss"],
    authEndpoint: "http://localhost:8000/broadcasting/auth",
    auth: { headers: { Authorization: `Bearer ${accessToken}` } },
});

echo.private(`user.${userId}`)
    .listen(".v1.notification.created", (data) => console.log(data.id, data.unread))
    .listen(".v1.user.admin.exported", (data) => console.log(data.fileUrl));
```

## Tenancy

With `IS_TENANCY=true`, each Event is a tenant.

| Item | Description |
|------|-------------|
| Tenant admin | `/api/v1/{tenant}/admin/*` with the same routes as the central admin |
| Public | `/api/v1/{tenant}/settings/variables`, `/api/v1/{tenant}/add-ons/variables` |
| Auth | Send `tenant` in the login, register, and forgot password body |
| Events | `/api/v1/admin/events` for the central superadmin |
| Add-ons | Features and modules per tenant. Disabled ones return 403 |

## Testing

```bash
composer test
```

## Scripts

| Script | Description |
|--------|-------------|
| `composer dev` | Start Octane with file watching |
| `composer start` | Start Octane on `0.0.0.0:8000` |
| `composer build` | Cache config, routes, and views |
| `composer test` | Run the PHPUnit suite |
| `composer lint` | Format with Pint |
| `composer pm2:start` | Start Octane, queue, scheduler, and Reverb with PM2 |

## Author

- Trip Teknologi ([@tripteki](https://linkedin.com/company/tripteki))
- Hasby Maulana ([@hsbmaulana](https://linkedin.com/in/hsbmaulana))
