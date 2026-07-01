<h1 align="center">Codebase Laravel</h1>

REST API for **Tripteki**, built with **Laravel 13**. Applies **Modular Monolith**, **Service Repository Pattern**, **DTO** (Spatie Laravel Data), **Event-Driven Architecture**, and **JWT authentication**.

### Features

| No | Feature | Description | Technology |
|----|---------|-------------|------------|
| 1 | REST API | Versioned endpoints under `/api/v1` | Laravel 13 + L5-Swagger 11 |
| 2 | Authentication | Dual JWT (`ACCESS_TOKEN` / `REFRESH_TOKEN`) | tymon/jwt-auth |
| 3 | Authorization | Role & permission per module (API-enforced) | Spatie Laravel Permission |
| 4 | User & Profile | `/users/me`, profile, interests, accesses | Modular `User` |
| 5 | User Admin | CRUD, verify, activate/deactivate, import/export | Modular `User` |
| 6 | Notifications | User & admin notification APIs, Web Push | Laravel Notifications + webpush |
| 7 | Import/Export | Async CSV/XLS/XLSX via queue | maatwebsite/excel |
| 8 | Real-time (optional) | Private broadcast channels | Laravel Reverb + Echo |
| 9 | Activity Log | User activity tracking | Spatie Laravel Activitylog |
| 10 | I18N | English, Malay, Indonesian | Laravel Localization |
| 11 | Validation | Style `422` `{ detail: [...] }` | Custom `ApiValidationResponse` |
| 12 | ULID | Sortable unique identifiers | Laravel HasUlids |
| 13 | Modular Structure | Domain modules under `src/` | nwidart/laravel-modules 13 |
| 14 | Query filtering | Request query sort/filter | spatie/laravel-query-builder |
| 15 | Rate limiting | `api-read` / `api-write` per route | Laravel RateLimiter |

Getting Started
---

### Requirements

- PHP >= 8.3
- Composer >= 2.7
- MySQL >= 8.0 (default) or PostgreSQL >= 14.x
- Redis (recommended for queue, cache, broadcast in production)

On Ubuntu/WSL, install PHP 8.3 before running artisan or tests:

```bash
sudo apt-get update
sudo apt-get install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-sqlite3 php8.3-bcmath php8.3-intl
sudo update-alternatives --set php /usr/bin/php8.3
php -v
```

### Installation

```bash
cd codebase-laravel.php

composer install

cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

### Configuration

Update `.env`:

```env
APP_NAME=tripteki
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000

OCTANE_HOST=127.0.0.1
OCTANE_PORT=8000

APP_TIMEZONE=Asia/Kuala_Lumpur
APP_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=base
DB_USERNAME=user
DB_PASSWORD=password

QUEUE_CONNECTION=redis

MM_CLIENT=phpredis
MM_HOST=127.0.0.1
MM_PORT=6379

FILESYSTEM_DISK=private

MAIL_DRIVER=smtp
APP_EMAIL_SERVER=tripteki.com
MAIL_ADMIN_ADDRESS=noreply@tripteki.com
MAIL_ADMIN_NAME=Admin

BROADCAST_CONNECTION=log

REVERB_APP_ID=tripteki
REVERB_APP_KEY=tripteki-key
REVERB_APP_SECRET=
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Web Push (optional)
VAPID_SUBJECT=mailto:noreply@tripteki.com
VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=
```

Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```

Default superuser (from seeder):

| Field | Value |
|-------|-------|
| Email | `superuser@{APP_EMAIL_SERVER}` (e.g. `superuser@tripteki.com`) |
| Password | `12345678` |

### Running the Application

#### Development (Octane)

```bash
composer dev
```

#### Queue worker (import/export, web push)

Import/export uses `user-admin-queue`. Web Push notifications use `notifications`:

```bash
php artisan queue:work --queue=user-admin-queue,notifications,default
```

#### Scheduler

Stale password-reset tokens and unverified users are cleaned every minute:

```bash
php artisan schedule:work
```

#### Production

```bash
composer build
```

Run Octane behind Nginx/Apache, plus queue worker and scheduler as daemon processes.

### API Documentation

Interactive Swagger UI:

```
http://localhost:8000/api/docs
```

### API Overview

All routes are prefixed with `/api`.

#### App

| Method | Path | Description |
|--------|------|-------------|
| GET | `/version` | Application version |
| GET | `/status` | Health check (memory, database, cache) |

#### Auth (`/v1/auth`)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| POST | `/register` | guest | Register (assigns `guest` role) |
| POST | `/login` | guest | Login → `{ accessToken, refreshToken, *Ttl }` |
| POST | `/logout` | access | Logout → `true` |
| PUT/PATCH | `/refresh` | refresh | Refresh token pair |
| GET | `/me` | access | Current user profile |
| POST | `/forgot-password` | guest | Send reset link |
| POST | `/reset-password` | guest | Reset with token body |
| POST | `/reset-password/{email}` | guest + signed | Reset via signed URL |
| POST | `/verify-email/{email}` | guest + signed | Verify email |
| POST | `/email/verification-notification` | access | Resend verification |

#### User & Profile (`/v1/users`)

Requires `auth:api`, `ACCESS_TOKEN`, `verified`.

| Method | Path | Description |
|--------|------|-------------|
| GET | `/me` | Current user + profile |
| PUT/PATCH | `/me` | Update user & profile |
| GET | `/me/accesses` | Roles and permissions |
| GET | `/me/interests` | Interest tags |
| POST | `/webpush/subscribe` | Register push subscription |
| POST | `/webpush/unsubscribe` | Remove push subscription |

#### User Admin (`/v1/admin/users`)

Requires `auth:api`, `ACCESS_TOKEN`, `verified`, and Spatie permissions (`user.*`).

| Method | Path | Permission | Description |
|--------|------|------------|-------------|
| GET | `/` | `user.view` | List users |
| POST | `/` | `user.create` | Create user |
| GET | `/{id}` | `user.view` | Show user |
| PUT/PATCH | `/{id}` | `user.update` | Update user |
| PUT/PATCH | `/verify/{id}` | `user.update` | Verify email |
| DELETE | `/deactivate/{id}` | `user.delete` | Soft delete |
| DELETE | `/activate/{id}` | `user.restore` | Restore |
| DELETE | `/force-delete/{id}` | `user.delete` | Force delete |
| POST | `/import` | `user.import` | Import CSV/XLS/XLSX |
| POST | `/export` | `user.export` | Export (`?type=` or `?export_type=`) |

#### Notifications

User routes: `/v1/notifications/*` (own notifications, verified).

User routes: `/v1/notifications/*` (own notifications, verified). Supports `filter[status]=read|unread`.

Admin routes: `/v1/admin/notifications/*` (requires `notification.view|delete|restore`).

### Rate limiting

| Limiter | Limit | Usage |
|---------|-------|-------|
| `api-read` | 10/min | `GET` routes |
| `api-write` | 30/min | `POST`, `PUT`, `PATCH`, `DELETE` |
| `api-refresh` | 30/min | Token refresh |
| `api-register` | 5/min | Registration |

### Authentication

JWT payload includes scope claim:

| Scope | Usage |
|-------|-------|
| `ACCESS_TOKEN` | Protected API routes |
| `REFRESH_TOKEN` | `PUT/PATCH /api/v1/auth/refresh` only |

Send header: `Authorization: Bearer {token}`

Validation errors return HTTP `422`:

```json
{
  "detail": [
    { "type": "missing", "loc": ["body", "email"], "msg": "...", "input": null, "ctx": {} }
  ]
}
```

Other errors use `{ "message": "..." }`.

### Authorization

Permissions are seeded per module and enforced on admin controllers. `superadmin` role bypasses all checks via `Gate::before`.

| Module | Permissions |
|--------|-------------|
| User | `user.view`, `user.create`, `user.update`, `user.delete`, `user.restore`, `user.import`, `user.export` |
| Notification | `notification.view`, `notification.delete`, `notification.restore` |
| Log | `activity.view`, `activity.delete` |

### Import / Export

- Formats: `csv`, `xls`, `xlsx`
- Column headers support i18n (`user.import.column.*`, `user.export.column.*`)
- Duplicate rows skipped by **email or name**
- API responds immediately with a started message; job runs on `user-admin-queue`
- On completion: database notification + broadcast event (when `BROADCAST_CONNECTION=reverb`)

### Broadcast (Laravel Reverb + Echo)

Admin import/export and activate/deactivate emit real-time events on private channel `user.{userId}`.

New in-app notifications also broadcast `v1.notification.created` (`{ id, unread }`).

#### Broadcast events

| Event | Payload (broadcast) |
|-------|---------------------|
| `v1.notification.created` | `id`, `unread` |
| `v1.user.admin.imported` | `userId`, `filename`, `totalImported`, `totalSkipped` |
| `v1.user.admin.imported-failed` | `userId`, `filename`, `error` |
| `v1.user.admin.exported` | `userId`, `filename`, `fileUrl`, `filePath` |
| `v1.user.admin.exported-failed` | `userId`, `error` |
| `v1.user.admin.activated` | `id`, `name`, `email`, `email_verified_at`, timestamps |
| `v1.user.admin.deactivated` | same as activated |

Channel authorization (`routes/channels.php`): user may only subscribe to `user.{id}` when JWT subject matches `{id}`.

Broadcast auth endpoint: `POST /broadcasting/auth` (requires `Authorization: Bearer {accessToken}` with `ACCESS_TOKEN` scope).

#### 1. Reverb setup

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=tripteki
REVERB_APP_KEY=tripteki-key
REVERB_APP_SECRET=
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

QUEUE_CONNECTION=redis
```

Run services:

```bash
# WebSocket server (Reverb)
php artisan reverb:start

# Process import/export and web push jobs
php artisan queue:work --queue=user-admin-queue,notifications,default
```

For debugging without WebSocket, keep `BROADCAST_CONNECTION=log` - payloads appear in `storage/logs`.

#### 2. Frontend dependencies

```bash
npm install laravel-echo pusher-js
```

Frontend `.env`:

```env
NEXT_PUBLIC_API_URL=http://localhost:8000
NEXT_PUBLIC_REVERB_APP_KEY=tripteki-key
NEXT_PUBLIC_REVERB_HOST=127.0.0.1
NEXT_PUBLIC_REVERB_PORT=8080
NEXT_PUBLIC_REVERB_SCHEME=http
```

#### 3. Bootstrap Echo

```javascript
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

export function createEcho(accessToken) {
  return new Echo({
    broadcaster: "reverb",
    key: process.env.NEXT_PUBLIC_REVERB_APP_KEY,
    wsHost: process.env.NEXT_PUBLIC_REVERB_HOST ?? "127.0.0.1",
    wsPort: Number(process.env.NEXT_PUBLIC_REVERB_PORT ?? 8080),
    wssPort: Number(process.env.NEXT_PUBLIC_REVERB_PORT ?? 8080),
    forceTLS: (process.env.NEXT_PUBLIC_REVERB_SCHEME ?? "http") === "https",
    enabledTransports: ["ws", "wss"],

    authEndpoint: `${process.env.NEXT_PUBLIC_API_URL}/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: `Bearer ${accessToken}`,
      },
    },
  });
}
```

Use the **access token** (not refresh token). Re-create Echo when the token is refreshed.

#### 4. Subscribe to admin events

After login, use the authenticated user's id as the channel name:

```javascript
const echo = createEcho(accessToken);
const userId = currentUser.id;

echo
  .private(`user.${userId}`)
  .listen(".v1.user.admin.imported", (payload) => {
    // { userId, filename, totalImported, totalSkipped }
  })
  .listen(".v1.user.admin.imported-failed", (payload) => {
    // { userId, filename, error }
  })
  .listen(".v1.user.admin.exported", (payload) => {
    // { userId, filename, fileUrl, filePath }
  })
  .listen(".v1.user.admin.exported-failed", (payload) => {
    // { userId, error }
  })
  .listen(".v1.user.admin.activated", (payload) => {
    // { id, name, email, email_verified_at, created_at, updated_at, deleted_at }
  })
  .listen(".v1.user.admin.deactivated", (payload) => {
    // same shape as activated
  });
```

Notes:

- Prefix the event with a dot (`.v1.user.admin.imported`) because events use custom `broadcastAs()` names.
- Subscribe with the **admin user's id** - the same user who called import/export/activate/deactivate APIs.
- Database notifications are still created independently; Echo is for instant UI updates.

#### 5. End-to-end flow (import example)

```
Frontend  →  POST /api/v1/admin/users/import
Backend   →  200 "User import started."
Queue     →  UserAdminImportJob on user-admin-queue
Job       →  event(UserAdminImported) → Reverb → Echo → frontend callback
Job       →  database notification (user.import.completed)
```

### Testing

```bash
composer test
```

Feature tests cover auth, user & profile, user admin, notifications, Web Push, ACL (403), and signed URL flows.

Project Structure
---

```
codebase-laravel.php/
├── app/
│   ├── Console/Kernel.php           # schedule: user:clean
│   ├── Exceptions/Handler.php       # 422 detail, 401/403/404 JSON
│   ├── Helpers/                     # UrlHelper, BrandingHelper, …
│   ├── Http/
│   └── Providers/                   # RouteServiceProvider (api-read / api-write)
├── routes/
│   ├── api.php                      # /version, /status
│   └── channels.php                 # user.{id} private channel
├── src/
│   ├── Auth/                        # JWT auth, mail, PasswordResetTokenHelper
│   ├── User/                        # Profile, admin, import/export, WebPush
│   ├── Notification/                # Inbox, admin, Reverb + WebPush
│   ├── Acl/                         # Roles (superadmin, admin, …)
│   ├── Log/                         # Activity log permissions
│   └── I18N/                        # Locale middleware
├── database/seeders/
├── tests/
└── lang/                            # en, ms, id (per module)
```

Author
---

- Trip Teknologi ([@tripteki](https://linkedin.com/company/tripteki))
- Hasby Maulana ([@hsbmaulana](https://linkedin.com/in/hsbmaulana))
