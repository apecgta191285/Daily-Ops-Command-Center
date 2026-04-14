# Daily Ops Command Center

Laravel 13 + Livewire 4 application for daily operations tracking, checklist execution, incident management, and checklist template administration.

## Stack

- PHP 8.4
- Laravel 13
- Livewire 4
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
composer test:browser
```

Browser smoke tests use Pest Browser + Playwright. On Linux/WSL hosts, Playwright also needs system browser libraries in addition to `npx playwright install chromium`. If those host dependencies are missing locally, use the GitHub Actions browser job as the authoritative execution surface until the machine is provisioned correctly.

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

Regeneration commands:

```bash
npm run build
```

## Notes

- Do not commit `.env`, `vendor`, `node_modules`, or runtime-generated files.
- Local development uses SQLite by default via `.env.example`.
- Public file attachments require the `public/storage` symlink created by `php artisan storage:link`.
- Public self-registration is intentionally unsupported. Accounts are provisioned internally.
- GitHub Actions workflows in `.github/workflows` expect repository secrets for Flux credentials when CI runs.
- Admin-only checklist template management now lives inside the same main application shell as the rest of the product at `/templates`, and legacy `/admin/*` checklist-template entry points are no longer part of the supported route contract.
- `DatabaseSeeder` exists for local bootstrap/demo narrative. Automated tests should prefer factories and scenario helpers instead of depending on seeded demo records.

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
- `docs/30_Product_Evolution_Roadmap_2026-04-14.md`
- `docs/31_Feature_Expansion_Plan_2026-04-14.md`
- `docs/32_F1_Dashboard_and_Triage_Execution_Pack_2026-04-14.md`
- `docs/33_F2_Incident_Triage_Execution_Pack_2026-04-14.md`
- `docs/34_F3_Checklist_UX_Execution_Pack_2026-04-14.md`
