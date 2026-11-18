# Fola Ojo Foundation — Website & CMS

Laravel 13 · Livewire 4 · Tailwind CSS 4 · MySQL 8

The public website for the Fola Ojo Foundation (FOF) and its admin dashboard. Administrators use the dashboard to manage content without writing code.

## Requirements

- PHP 8.3+ with the `gd`, `pdo_mysql`, `mbstring`, `fileinfo`, `exif` and `zip` extensions
- Composer 2
- Node.js 20+ and npm
- MySQL 8 (or MariaDB 10.6+)

## Local installation

```bash
composer install
cp .env.example .env          # then set DB_* values
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build                 # or: npm run dev
php artisan serve
```

Visit `http://localhost:8000`. The admin dashboard is at `http://localhost:8000/admin`.

Notification emails are queued. During development, run a worker:

```bash
php artisan queue:work
```

## Seeded content

`php artisan db:seed` loads the foundation's real content: home, about, get-involved and contact pages, five programmes, the founder, and the founder's introductory letter as the first article. The seeder never overwrites existing records, so it is safe to run again.

Placeholder photographs come from Unsplash and are processed into responsive WebP images. Credits are in `database/seeders/images/CREDITS.md`. Replace them with the foundation's own photographs through the admin dashboard.

> `migrate:fresh --seed` doesn't delete previously generated images in `storage/app/public`. Clear `storage/app/public/{pages,programmes,posts}` first if you reseed often.

## Default administrator (local development only)

| Email | Password |
|---|---|
| `admin@folaojofoundation.org` | `ChangeMe@2026` |

You must change this password the first time you sign in. Before seeding production, set `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` in `.env`. **Never use the default credentials in production.**

## Roles

| Role | Access |
|---|---|
| Super Admin | Everything, including user management and the activity log |
| Administrator | All content, enquiries, subscribers and site settings |
| Content Editor | Blog, programmes, gallery and page content only |

## Testing

Tests use an in-memory SQLite database:

```bash
php artisan test
```

## Project structure

| Path | Purpose |
|---|---|
| `app/Enums` | Roles, permissions and status enums |
| `app/Livewire/Admin` | Admin dashboard components |
| `app/Services` | `SettingsService`, `ImageService`, `ActivityLogger` |
| `app/Support` | Shared traits (`HasSlug`, `LogsActivity`, `WithToasts`) and admin navigation |
| `config/foundation.php` | Declares every editable site setting and its default |
| `routes/admin.php` | Admin routes (`/admin`, named `admin.*`) |
| `resources/views/components/ui` | Reusable UI components |
