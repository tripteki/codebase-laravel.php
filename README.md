<h1 align="center">Codebase Laravel</h1>

This skeleton provides comprehensive fullstack implementation built with Laravel framework, applying concepts like `Test Driven Development` `Event-Driven Architecture`, implementing design principles like `SOLID` `RESTful API` `Modular Monolith` `Dependency Injection`, and design patterns like `Data Transfer Object (DTO)` `Service Repository Pattern` `Observer Pattern`.

### Features

<table style="width: 100%; border: none;">
  <tr>
    <th>No</th>
    <th>Feature</th>
    <th>Description</th>
    <th>Technology</th>
  </tr>
  <tr>
    <td>1</td>
    <td>REST API</td>
    <td>RESTful API with comprehensive endpoints</td>
    <td>Laravel 10 + API Resources</td>
  </tr>
  <tr>
    <td>2</td>
    <td>Admin Panel</td>
    <td>Modern admin dashboard with CRUD operations</td>
    <td>Filament 3.x</td>
  </tr>
  <tr>
    <td>3</td>
    <td>Authentication</td>
    <td>JWT-based authentication with session support</td>
    <td>Laravel Sanctum + JWT</td>
  </tr>
  <tr>
    <td>4</td>
    <td>Authorization</td>
    <td>Role-based access control with permissions</td>
    <td>Spatie Laravel Permission (ULID)</td>
  </tr>
  <tr>
    <td>5</td>
    <td>Activity Log</td>
    <td>Track user activities and changes</td>
    <td>Spatie Laravel Activitylog (ULID)</td>
  </tr>
  <tr>
    <td>6</td>
    <td>Import/Export</td>
    <td>Bulk data import/export with CSV/XLSX support</td>
    <td>Filament Import/Export + Queue</td>
  </tr>
  <tr>
    <td>7</td>
    <td>Database</td>
    <td>Relational database with migrations</td>
    <td>MySQL/PostgreSQL + Eloquent ORM</td>
  </tr>
  <tr>
    <td>8</td>
    <td>Notifications</td>
    <td>Database notifications for async operations</td>
    <td>Laravel Notifications + Observer</td>
  </tr>
  <tr>
    <td>9</td>
    <td>Internationalization</td>
    <td>Multi-language support (English + Indonesian)</td>
    <td>Laravel Localization</td>
  </tr>
  <tr>
    <td>10</td>
    <td>Testing</td>
    <td>Feature and unit testing</td>
    <td>PHPUnit + Laravel Testing</td>
  </tr>
  <tr>
    <td>11</td>
    <td>ULID Support</td>
    <td>Universally Unique Lexicographically Sortable Identifiers</td>
    <td>Laravel HasUlids</td>
  </tr>
  <tr>
    <td>12</td>
    <td>Modular Structure</td>
    <td>Clean separation of concerns with modular architecture</td>
    <td>Custom Module System</td>
  </tr>
</table>

Getting Started
---

### Requirements

Ensure you have the following installed:

- PHP >= 8.2
- Composer >= 2.7
- MySQL >= 8.0 or PostgreSQL >= 14.x
- Node.js >= 20.x
- NPM >= 10.x

### Installation

```bash
# Clone the repository
git clone <repository-url>
cd codebase-laravel.php

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Configuration

Copy the environment configuration file and update with your credentials:

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Run database migrations and seeders:

```bash
# Run migrations
php artisan migrate

# Seed database with default data
php artisan db:seed
```

Default credentials:
- Email: `superuser@mail.com`
- Password: `12345678`

### Running the Application

#### Development Mode

```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite for Filament
npm run dev
```

The application will be available at:
- **API**: `http://localhost:8000/api`
- **Admin Panel**: `http://localhost:8000/admin`

#### Production Mode

```bash
# Build assets
npm run build

# Optimize application
php artisan optimize
php artisan filament:optimize

# Start production server (use with Nginx/Apache)
```

#### Queue Worker

Start queue worker to process background jobs (import/export):

```bash
# Start queue worker
php artisan queue:work

# Start with retry and timeout options
php artisan queue:work --tries=3 --timeout=90
```

### API Documentation

Access the interactive API documentation at:

```
http://localhost:8000/api/docs
```

Features:
- Interactive endpoint testing
- Request/response examples
- Schema definitions
- Authentication support

### Admin Panel

Access the Filament admin panel at:

```
http://localhost:8000/admin
```

Features:
- Dashboard with statistics
- User Management (CRUD)
- Role & Permission Management
- Activity Log Viewer
- Import/Export Users
- Real-time Notifications

#### Available Modules

- **Dashboard**: Overview and statistics
- **User Management**: Manage user accounts
- **Access Control**: 
  - Roles: Define user roles
  - Permissions: Manage permissions
- **Activity Log**: Track all user activities

### Notifications

This application provides database notifications for import/export operations.

#### How It Works

When an import/export operation completes:
1. Observer detects completion
2. Notification sent to database
3. User sees notification in bell icon
4. Download links available (for exports)

#### Notification Types

- **Import Completed**: Shows success/failed rows count
- **Export Completed**: Provides CSV/XLSX download links

### Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage
```

Project Structure
---

```
codebase-laravel.php/
├── app/
│   ├── Models/                    # Eloquent models
│   ├── Observers/                 # Model observers
│   │   ├── ExportCompletedObserver.php
│   │   └── ImportCompletedObserver.php
│   └── Providers/                 # Service providers
├── src/
│   └── V1/
│       ├── Api/                   # API modules
│       │   ├── Auth/              # Authentication
│       │   ├── User/              # User management
│       │   ├── Acl/               # Access Control
│       │   └── Log/               # Activity logging
│       └── Web/
│           └── Filament/          # Admin panel
│               ├── Resources/     # Filament resources
│               ├── Exports/       # Export classes
│               └── Imports/       # Import classes
├── database/
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Database seeders
├── tests/                         # Test files
│   └── Feature/
│       └── V1/Api/
└── lang/                          # Translations (EN/ID)
```

Author
---

- Trip Teknologi ([@tripteki](https://linkedin.com/company/tripteki))
- Hasby Maulana ([@hsbmaulana](https://linkedin.com/in/hsbmaulana))
