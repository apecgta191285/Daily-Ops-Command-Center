# Daily Ops Command Center

Daily Ops Command Center is an internal web application for university computer lab teams. It helps duty staff complete routine checks, report room or equipment issues, and lets lab supervisors monitor the day’s work from one shared workboard.

Current product stance:

- internal provisioning only
- 3 roles only: `admin`, `supervisor`, `staff`
- single-organization demo baseline
- no public sign-up
- no multi-tenant model
- disciplined MVP+ / strong capstone, not an enterprise platform

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

## Documentation Reading Order

Read these first when you need current repo truth:

1. `docs/00_Project_Lock_v1.1.md`
2. `docs/01_Product_Brief_v1.1.md`
3. `docs/02_System_Spec_v0.3.md`
4. `docs/04_Current_State_v1.3.md`

Document roles:

- canonical truth: `00`, `01`, `02`, `04`, plus `05`, `06`, `22`, `24`, `26` when a contract changes
- execution history: numbered execution packs such as `39-102`
- external or AI analysis references: ad hoc audit files under `docs/` are reference inputs only and must never override repo source-of-truth documents

## Product Baseline

Current repository capabilities:

- scope-aware daily checklist runtime for opening, during-day, and closing work
- incident reporting with lightweight attachment support
- management incident queue, detail, accountability, and history review
- admin template governance by scope
- admin-owned user lifecycle inside the main app shell
- dashboard workboard built from real checklist, incident, and history signals
- print-friendly checklist recap and incident summary for review/demo evidence

Out of scope by design:

- public registration
- multi-tenant model
- notification engine
- approval workflow
- analytics warehouse or report builder
- enterprise asset platform
- AI/copilot layer

## Demo Walkthrough

For local/manual demos with seeded data:

1. Log in as `operatora@example.com` / `password` to show duty-staff checklist execution and incident reporting.
2. Log in as `supervisor@example.com` / `password` to show dashboard attention states, incident follow-up, and printable evidence surfaces.
3. Log in as `admin@example.com` / `password` to show checklist template governance, user administration, and the UI governance guide.

The seeded narrative is intentionally small and realistic: one live opening checklist, a few active/resolved issues, recent history, and internal accounts that support a believable university computer lab demo.

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

Regeneration command:

```bash
npm run build
```

## Notes

- Do not commit `.env`, `vendor`, `node_modules`, or runtime-generated files.
- Local development uses SQLite by default via `.env.example`.
- Public file attachments require the `public/storage` symlink created by `php artisan storage:link`.
- Public self-registration is intentionally unsupported. Accounts are provisioned internally.
- `DatabaseSeeder` exists for local bootstrap/demo narrative. Automated tests should prefer factories and scenario helpers instead of depending on seeded demo records.
