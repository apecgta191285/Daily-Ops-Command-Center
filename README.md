# Daily Ops Command Center

Daily Ops Command Center is an internal web application for university computer lab operations. In the current case study, one responsible lecturer oversees several computer labs or rooms, room caretakers supervise daily conditions, and students on duty carry out routine checks and report problems.

Current product stance: internal provisioning only, 3 roles only (`admin`, `supervisor`, `staff`), single-organization demo baseline, no public sign-up, no multi-tenant model, and a disciplined MVP+ / strong capstone stance rather than an enterprise-platform claim. The real case study assumes multiple university computer labs or rooms, and the current implementation already uses room-centered operations with time scope preserved as the second dimension.

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
- execution history: numbered execution packs
- external or AI analysis references: ad hoc audit files under `docs/` are reference inputs only and must never override repo source-of-truth documents

## Product Baseline

Current repository capabilities:

- room-aware daily checklist runtime for opening, during-day, and closing work
- incident reporting with room context, optional lightweight equipment reference, and lightweight attachment support
- management incident queue, detail, accountability, and history review with room context
- admin template governance by scope
- admin-owned user lifecycle inside the main app shell
- dashboard workboard built from real checklist, incident, room, and history signals
- print-friendly checklist recap and incident summary for review/demo evidence with room context

Current actor mapping for the case study:

- `admin` = responsible lecturer or authorized academic owner
- `supervisor` = lab boy, lab staff member, or room caretaker
- `staff` = students assigned to check rooms on duty

Current case-study framing:

- the real operating context involves multiple university computer labs or rooms
- the current implementation baseline is `room + time scope`, not time scope only
- room-centered lab operations are already landed in the current repository baseline
- a full machine registry is future work and is explicitly out of scope right now

Known limitations that still matter:

- optional equipment reference is lightweight free text, not a machine registry
- dashboard/workboard is room-aware, but it is not a deep room-machine intelligence board
- authenticated browser coverage is meaningful but not a full visual envelope for every heavy screen
- the system is still not a production-grade platform claim

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

1. Log in as `operatora@example.com` / `password` to show a student on duty completing room checks and reporting issues found during a lab round.
2. Log in as `supervisor@example.com` / `password` to show a lab caretaker reviewing dashboard attention states, incident follow-up, and printable evidence surfaces across the day.
3. Log in as `admin@example.com` / `password` to show the responsible lecturer or authorized academic owner governing checklist templates, users, and UI guidance inside the same system.

The seeded narrative is intentionally small and realistic: it is a compact university computer lab demo baseline that already shows room-centered operations across several rooms, while still stopping short of machine-registry management.

## Repo Notes

GitHub Actions expects repository secrets for Flux package access:

- `FLUX_USERNAME`
- `FLUX_LICENSE_KEY`

Without these secrets, dependency installation in CI will fail before lint or tests run.

- Do not commit `.env`, `vendor`, `node_modules`, or runtime-generated files.
- Local development uses SQLite by default via `.env.example`.
- Public file attachments require the `public/storage` symlink created by `php artisan storage:link`.
- Public self-registration is intentionally unsupported. Accounts are provisioned internally.
- `DatabaseSeeder` exists for local bootstrap/demo narrative. Automated tests should prefer factories and scenario helpers instead of depending on seeded demo records.
- Vite build output under `public/build` is not tracked. Regenerate it with `npm run build`.
