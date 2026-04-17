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
- Checklist templates should be duplicated before major structural changes when you want a safer revision path. Duplicated templates are created inactive so they can be reviewed before replacing the live daily template.
- Checklist items may use optional group labels to create lightweight sections in the daily run surface without introducing a full checklist-builder hierarchy.
- Daily checklist now shows lightweight anomaly memory when the same item was recently marked `Not Done`, so staff can spot recurring issues without opening a full analytics layer.
- Incident detail now highlights the latest follow-up direction and latest resolution summary so management users can understand the most recent handling context quickly.
- Incident reporting now ends with an outcome screen that explains what was submitted and what happens next, instead of relying on a success flash alone.
- Incident stale-threshold logic now has one owner, and incident list filtering is routed through an application query instead of staying embedded in the Livewire component.
- Dashboard now includes checklist and incident intake trends plus category hotspot summaries so management can scan operational pressure faster.
- Template administration now shows live activation impact cues before save, so admins can see when a draft will replace the current live checklist.
- Checklist follow-up handoff now uses one prefill contract shared by both the daily checklist surface and the incident create surface, so future checklist-to-incident context can grow without re-embedding query-shaping logic in Livewire components.
- Frontend contract hardening has started: shared visual tokens now cover subtle surfaces, danger/brand actions, motion timing, shadows, and radius scales, while alert feedback can dismiss cleanly without page reloads.
- `DatabaseSeeder` exists for local bootstrap/demo narrative. Automated tests should prefer factories and scenario helpers instead of depending on seeded demo records.

## Demo Walkthrough

For local/manual demos with seeded data:

1. Log in as `operatora@example.com` / `password` to show checklist execution and incident reporting.
2. Log in as `supervisor@example.com` / `password` to show dashboard attention states and incident follow-up.
3. Log in as `admin@example.com` / `password` to show checklist template administration inside the main app shell.

The seeded narrative is intentionally small and realistic: it includes one active opening template, a mix of open/in-progress/resolved incidents, and recent history that supports dashboard, checklist, and triage walkthroughs.

See also:

- `docs/36_F5_Selective_Delivery_Hardening_Execution_Pack_2026-04-14.md`
- `docs/37_Local_Demo_Runbook_2026-04-14.md`
- `docs/38_Post_F5_Product_and_Codebase_Audit_2026-04-14.md`
- `docs/39_N1_Template_Duplication_and_Iteration_Safety_Execution_Pack_2026-04-16.md`
- `docs/40_N2_Lightweight_Checklist_Grouping_Execution_Pack_2026-04-16.md`
- `docs/41_N3_Incident_Follow_Up_Quality_Layer_Execution_Pack_2026-04-16.md`
- `docs/42_N4_Demo_Friendly_Outcome_Screens_Execution_Pack_2026-04-16.md`
- `docs/43_R1_R2_Incident_Query_and_Stale_Policy_Execution_Pack_2026-04-16.md`
- `docs/44_Post_N4_Product_and_Codebase_Audit_2026-04-16.md`
- `docs/45_N5_Dashboard_Trend_and_Hotspot_Layer_Execution_Pack_2026-04-16.md`
- `docs/47_R3_N6_Template_Manage_Refactor_and_Activation_Cues_Execution_Pack_2026-04-16.md`
- `docs/48_R5_Checklist_Incident_Prefill_Extraction_Execution_Pack_2026-04-17.md`
- `docs/49_N7_Checklist_Anomaly_Memory_Execution_Pack_2026-04-17.md`
- `docs/46_R4_Dashboard_Assembly_Extraction_Execution_Pack_2026-04-16.md`
- `docs/50_Frontend_Engineering_Product_Wave_Strategy_2026-04-17.md`
- `docs/51_FE1_Frontend_Contract_Hardening_Execution_Pack_2026-04-17.md`

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
- `docs/35_F4_Product_Framing_and_Demo_Quality_Execution_Pack_2026-04-14.md`
- `docs/36_F5_Selective_Delivery_Hardening_Execution_Pack_2026-04-14.md`
- `docs/37_Local_Demo_Runbook_2026-04-14.md`
- `docs/38_Post_F5_Product_and_Codebase_Audit_2026-04-14.md`
- `docs/39_N1_Template_Duplication_and_Iteration_Safety_Execution_Pack_2026-04-16.md`
- `docs/40_N2_Lightweight_Checklist_Grouping_Execution_Pack_2026-04-16.md`
- `docs/41_N3_Incident_Follow_Up_Quality_Layer_Execution_Pack_2026-04-16.md`
- `docs/42_N4_Demo_Friendly_Outcome_Screens_Execution_Pack_2026-04-16.md`
- `docs/43_R1_R2_Incident_Query_and_Stale_Policy_Execution_Pack_2026-04-16.md`
- `docs/44_Post_N4_Product_and_Codebase_Audit_2026-04-16.md`
- `docs/45_N5_Dashboard_Trend_and_Hotspot_Layer_Execution_Pack_2026-04-16.md`
- `docs/47_R3_N6_Template_Manage_Refactor_and_Activation_Cues_Execution_Pack_2026-04-16.md`
- `docs/48_R5_Checklist_Incident_Prefill_Extraction_Execution_Pack_2026-04-17.md`
- `docs/49_N7_Checklist_Anomaly_Memory_Execution_Pack_2026-04-17.md`
- `docs/50_Frontend_Engineering_Product_Wave_Strategy_2026-04-17.md`
- `docs/51_FE1_Frontend_Contract_Hardening_Execution_Pack_2026-04-17.md`
- `docs/46_R4_Dashboard_Assembly_Extraction_Execution_Pack_2026-04-16.md`
