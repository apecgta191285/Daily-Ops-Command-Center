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

3. Prepare the local SQLite database, storage symlink, and schema:

```bash
touch database/database.sqlite
php artisan storage:link
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

## CI Prerequisites

GitHub Actions expects repository secrets for Flux package access:

- `FLUX_USERNAME`
- `FLUX_LICENSE_KEY`

Without these secrets, dependency installation in CI will fail before lint or tests run.

## Artifact Policy

Tracked in git:

- application source code
- Blade views and component overrides
- app-owned static public files such as `favicon`, `robots.txt`, and root web entrypoints

Not tracked in git:

- Vite build output under `public/build`
- vendor-generated Filament assets under `public/js/filament`, `public/css/filament`, and `public/fonts/filament`

Regeneration commands:

```bash
npm run build
php artisan filament:upgrade
```

## Notes

- Do not commit `.env`, `vendor`, `node_modules`, or runtime-generated files.
- Local development uses SQLite by default via `.env.example`.
- Public file attachments require the `public/storage` symlink created by `php artisan storage:link`.
- Public self-registration is intentionally unsupported. Accounts are provisioned internally.
- GitHub Actions workflows in `.github/workflows` expect repository secrets for Flux credentials when CI runs.

## Canonical Documentation

The repository keeps only long-lived documentation that still acts as a source of truth.

- `docs/00_Project_Lock_v1.1.md`
- `docs/01_Product_Brief_v1.1.md`
- `docs/02_System_Spec_v0.3.md`
- `docs/03_Evaluation_Protocol_v1.1.md`
- `docs/04_Current_State_v1.3.md`
- `docs/05_Decision_Log_v1.3.md`
- `docs/06_Data_Definition_v1.2.md`
- `docs/22_Architecture_Boundary_and_Execution_Standards_2026-04-11.md`
- `docs/24_Domain_Normalization_Design_2026-04-11.md`
- `docs/26_Architecture_Debt_Roadmap_2026-04-11.md`
