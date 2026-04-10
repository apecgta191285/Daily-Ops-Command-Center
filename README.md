# Daily Ops Command Center

Laravel 13 + Livewire 4 + Filament 5 application for daily operations tracking, checklist execution, and incident management.

## Stack

- PHP 8.4
- Laravel 13
- Livewire 4
- Filament 5
- Vite
- SQLite for local development

## Local Setup

1. Install PHP and Node dependencies:

```bash
composer install
npm install
```

2. Create your environment file and app key:

```bash
cp .env.example .env
php artisan key:generate
```

3. Prepare the database and run migrations:

```bash
touch database/database.sqlite
php artisan migrate
```

4. Start the app:

```bash
composer dev
```

## Quality Checks

```bash
composer lint
php artisan test
```

## Notes

- Do not commit `.env`, `vendor`, `node_modules`, or runtime-generated files.
- GitHub Actions workflows in `.github/workflows` expect repository secrets for Flux credentials when CI runs.
