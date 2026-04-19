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
- `WF1 Scoped Daily Operations Runtime` is now visible on the management dashboard as well: dashboard signals now show checklist lane truth by opening, midday, and closing scope so missing or incomplete live coverage is no longer hidden behind aggregate totals.
- Template administration now also reads as a scope-governance surface: admins can see live runtime ownership by opening, midday, and closing lane instead of interpreting scope only from a flat template table.
- WF1 is now closed as a complete product wave: the repository’s canonical docs, decision history, and system/data references now match the scoped runtime that already exists in code.
- WF2 has now started landing as the next usefulness wave: `WF2-A Incident Ownership Lite` adds optional management owner and follow-up target accountability to incidents without crossing into enterprise assignment, escalation, or notification systems.
- WF2-B is now live as well: the management incident queue can filter by unowned, mine, and overdue follow-up while incident detail surfaces now flag ownership pressure more explicitly.
- WF2-C is now live on the dashboard too: management can see ownership pressure from the command surface through unowned, overdue, and actor-owned accountability signals without turning the dashboard into a reporting product.
- WF2 is now closed as a complete product wave: incident accountability is now part of the repository's canonical truth across persistence, queue, detail, dashboard, and long-lived documentation without expanding into notifications, SLA math, or enterprise assignment workflow.
- WF3 has now started landing as the next operability wave: the application layer now owns internal user provisioning and lifecycle updates, so account creation, role changes, active/inactive changes, and explicit admin password set/reset no longer depend on manual database edits.
- WF3-B is now live as well: admins can operate the internal user roster from inside the main app shell through `/users`, provision new accounts, and update role/active/password lifecycle without falling back to manual database edits or pseudo-admin pages.
- WF3-C is now live as well: administrator lifecycle now has app-owned guard rails that block self-deactivation, self-demotion, and removal of the last active admin while keeping the workflow lightweight and internal.
- WF3 is now closed as a complete product wave: user lifecycle is part of the canonical repository truth across application owners, admin surfaces, guard rails, regression proof, and long-lived documentation without expanding into invitations, RBAC matrix design, or external identity integration.
- WF4 planning is now locked as the next usefulness wave: the next step is a lightweight operational history layer centered on checklist run archive and recap review, not a reporting warehouse or analytics product.
- Frontend contract hardening has started: shared visual tokens now cover subtle surfaces, danger/brand actions, motion timing, shadows, and radius scales, while alert feedback can dismiss cleanly without page reloads.
- Frontend component language now includes reusable stat cards, empty states, callouts, chips, and timeline shells so major product surfaces can evolve on shared primitives instead of ad-hoc markup.
- Frontend FE3 surface redesign now gives the dashboard, daily checklist, and template manage screens a stronger command-surface composition so the product reads as one intentional system instead of a collection of forms and cards.
- Frontend FE4 polish now adds skip links, consistent focus-visible behavior, and mobile-friendly table stacking for the main data-heavy screens so the product feels more finished beyond desktop-only viewing.
- Frontend FE5 now commits the product to one flagship visual theme, retires the half-implemented appearance switch, strengthens typography identity with a display layer, and removes the most visible hardcoded surface residue that was still undermining consistency.
- Frontend FE6 has started delivering screen-depth upgrades through a dashboard signal-depth pass that makes the command view read faster with stronger glance cards, clearer trend emphasis, and ranked hotspot scanability without introducing heavy analytics infrastructure.
- Frontend FE6 now also upgrades incident detail into a stronger narrative surface, so latest follow-up context, evidence, action lane, and timeline sequence read as one operational story instead of disconnected cards.
- Frontend FE6 now also adds app-owned motion orchestration across key surfaces, so dashboard, incident detail, and template authoring reveal with intentional cadence while still respecting reduced-motion and Livewire navigation constraints.
- Frontend FE6 now also cleans up the settings family so profile, security, recovery-code, and destructive-account flows read as one calm control surface instead of leftover utility pages beside the main product.
- Frontend FE6 now also deepens template authoring into a clearer admin workspace, with authoring checkpoints, live execution preview, and stronger item-level scanability so checklist drafting feels intentional before activation.
- Frontend FE7 now adds app-owned visual data primitives, stronger dashboard atmosphere, signal hover depth, and cleaner recap/template surface contracts so the product reads more like a precision command surface than a styled CRUD app.
- Frontend FE8 now completes staggered reveal orchestration, hotspot meter animation, investigation-weighted incident detail treatment, and font preload hardening while keeping the single flagship theme intact.
- Frontend FE9 has started with an app-shell architecture repair that restores the authenticated Flux sidebar/header/main relationship, so the left rail acts as the real application frame instead of a detached top-left block.
- Frontend FE9 now also redesigns auth and welcome entry surfaces into a stronger command-entry experience, so the product’s first impression finally matches the richer operational language already present inside dashboard, incident, checklist, and admin screens.
- Frontend FE9 now also assimilates the major authenticated screens under one shared shell-intro rhythm, so dashboard, incidents, templates, and staff runtime read more like one product family instead of isolated polished pages.
- Frontend FE9 now also finishes the remaining workflow seams by applying the same premium shell-intro framing to incident detail, template authoring, and staff incident reporting, then closes the round with visual QA-driven consistency checks.
- Frontend CSS architecture has now been split into concern-based `ops` and `settings` modules behind the same import contract, so the product keeps its current UI while becoming much easier to maintain and review safely.
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
- `docs/52_FE2_Component_Language_Expansion_Execution_Pack_2026-04-17.md`
- `docs/53_FE3_Dashboard_Checklist_Template_Surface_Redesign_Execution_Pack_2026-04-17.md`
- `docs/54_FE4_Feedback_Accessibility_and_Responsive_Polish_Execution_Pack_2026-04-17.md`
- `docs/55_Post_FE4_Frontend_Engineering_Audit_and_Next_Wave_Strategy_2026-04-17.md`
- `docs/56_FE5_Frontend_Identity_and_Theme_Contract_Resolution_Execution_Pack_2026-04-17.md`
- `docs/57_Post_FE5_Frontend_Engineering_Audit_and_FE6_Strategy_2026-04-17.md`
- `docs/58_FE6_Dashboard_Signal_Depth_Execution_Pack_2026-04-17.md`
- `docs/59_FE6_Incident_Detail_Narrative_Surface_Execution_Pack_2026-04-17.md`
- `docs/60_FE6_Template_Authoring_Surface_Depth_Execution_Pack_2026-04-17.md`
- `docs/61_FE6_Motion_and_Reveal_Orchestration_Execution_Pack_2026-04-17.md`
- `docs/62_FE6_Settings_Surface_Cleanup_Execution_Pack_2026-04-18.md`
- `docs/63_FE8_Frontend_Hardening_and_CSS_Architecture_Split_Execution_Pack_2026-04-18.md`
- `docs/65_FE9_Premium_UI_Shell_and_Identity_Master_Plan_2026-04-18.md`
- `docs/66_FE9_App_Shell_Architecture_Repair_Execution_Pack_2026-04-18.md`
- `docs/67_FE9_Auth_and_Welcome_Identity_Redesign_Execution_Pack_2026-04-18.md`
- `docs/68_FE9_Cross_Screen_Shell_Assimilation_Execution_Pack_2026-04-18.md`
- `docs/69_FE9_Premium_UI_Finish_and_Visual_QA_Execution_Pack_2026-04-18.md`
- `docs/70_Full_Stack_Product_Evolution_Audit_and_Next_Wave_Master_Plan_2026-04-18.md`
- `docs/71_WF1_Scoped_Daily_Operations_Runtime_Master_Plan_2026-04-18.md`
- `docs/72_WF1_A_Domain_and_Runtime_Realignment_Execution_Pack_2026-04-18.md`
- `docs/73_WF1_A_WF1_B_Scope_Runtime_Entry_Execution_Pack_2026-04-18.md`
- `docs/74_WF1_C_Scope_Aware_Dashboard_and_Signals_Execution_Pack_2026-04-18.md`
- `docs/75_WF1_D_Template_Administration_Upgrade_Execution_Pack_2026-04-19.md`
- `docs/76_WF1_E_Quality_Hardening_and_Documentation_Execution_Pack_2026-04-19.md`
- `docs/77_WF2_Incident_Ownership_Lite_Master_Plan_2026-04-19.md`
- `docs/78_WF2_A_Ownership_and_Follow_Up_Core_Execution_Pack_2026-04-19.md`
- `docs/79_WF2_A_Incident_Accountability_Core_Execution_Pack_2026-04-19.md`
- `docs/80_WF2_B_Queue_and_Detail_Surface_Upgrade_Execution_Pack_2026-04-19.md`
- `docs/81_WF2_C_Dashboard_Ownership_Pressure_Execution_Pack_2026-04-19.md`
- `docs/82_WF2_D_Quality_Hardening_and_Documentation_Execution_Pack_2026-04-19.md`
- `docs/83_WF3_User_Administration_Lite_Master_Plan_2026-04-19.md`
- `docs/84_WF3_A_User_Lifecycle_and_Provisioning_Core_Execution_Pack_2026-04-19.md`
- `docs/85_WF3_A_User_Lifecycle_and_Provisioning_Core_Implementation_Execution_Pack_2026-04-19.md`
- `docs/86_WF3_B_User_Administration_Surface_Execution_Pack_2026-04-19.md`
- `docs/87_WF3_C_Account_Safety_and_Access_Guard_Rails_Execution_Pack_2026-04-19.md`
- `docs/88_WF3_D_Quality_Hardening_and_Documentation_Execution_Pack_2026-04-19.md`
- `docs/89_WF4_Operational_History_and_Run_Archive_Master_Plan_2026-04-19.md`
- `docs/90_WF4_A_Checklist_Run_Archive_Core_Execution_Pack_2026-04-19.md`

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
- `docs/52_FE2_Component_Language_Expansion_Execution_Pack_2026-04-17.md`
- `docs/53_FE3_Dashboard_Checklist_Template_Surface_Redesign_Execution_Pack_2026-04-17.md`
- `docs/54_FE4_Feedback_Accessibility_and_Responsive_Polish_Execution_Pack_2026-04-17.md`
- `docs/55_Post_FE4_Frontend_Engineering_Audit_and_Next_Wave_Strategy_2026-04-17.md`
- `docs/56_FE5_Frontend_Identity_and_Theme_Contract_Resolution_Execution_Pack_2026-04-17.md`
- `docs/57_Post_FE5_Frontend_Engineering_Audit_and_FE6_Strategy_2026-04-17.md`
- `docs/58_FE6_Dashboard_Signal_Depth_Execution_Pack_2026-04-17.md`
- `docs/59_FE6_Incident_Detail_Narrative_Surface_Execution_Pack_2026-04-17.md`
- `docs/60_FE6_Template_Authoring_Surface_Depth_Execution_Pack_2026-04-17.md`
- `docs/61_FE6_Motion_and_Reveal_Orchestration_Execution_Pack_2026-04-17.md`
- `docs/62_FE6_Settings_Surface_Cleanup_Execution_Pack_2026-04-18.md`
- `docs/63_FE8_Frontend_Hardening_and_CSS_Architecture_Split_Execution_Pack_2026-04-18.md`
- `docs/65_FE9_Premium_UI_Shell_and_Identity_Master_Plan_2026-04-18.md`
- `docs/66_FE9_App_Shell_Architecture_Repair_Execution_Pack_2026-04-18.md`
- `docs/67_FE9_Auth_and_Welcome_Identity_Redesign_Execution_Pack_2026-04-18.md`
- `docs/68_FE9_Cross_Screen_Shell_Assimilation_Execution_Pack_2026-04-18.md`
- `docs/69_FE9_Premium_UI_Finish_and_Visual_QA_Execution_Pack_2026-04-18.md`
- `docs/70_Full_Stack_Product_Evolution_Audit_and_Next_Wave_Master_Plan_2026-04-18.md`
- `docs/71_WF1_Scoped_Daily_Operations_Runtime_Master_Plan_2026-04-18.md`
- `docs/72_WF1_A_Domain_and_Runtime_Realignment_Execution_Pack_2026-04-18.md`
- `docs/73_WF1_A_WF1_B_Scope_Runtime_Entry_Execution_Pack_2026-04-18.md`
- `docs/74_WF1_C_Scope_Aware_Dashboard_and_Signals_Execution_Pack_2026-04-18.md`
- `docs/75_WF1_D_Template_Administration_Upgrade_Execution_Pack_2026-04-19.md`
- `docs/76_WF1_E_Quality_Hardening_and_Documentation_Execution_Pack_2026-04-19.md`
- `docs/77_WF2_Incident_Ownership_Lite_Master_Plan_2026-04-19.md`
- `docs/78_WF2_A_Ownership_and_Follow_Up_Core_Execution_Pack_2026-04-19.md`
- `docs/79_WF2_A_Incident_Accountability_Core_Execution_Pack_2026-04-19.md`
- `docs/80_WF2_B_Queue_and_Detail_Surface_Upgrade_Execution_Pack_2026-04-19.md`
- `docs/81_WF2_C_Dashboard_Ownership_Pressure_Execution_Pack_2026-04-19.md`
- `docs/82_WF2_D_Quality_Hardening_and_Documentation_Execution_Pack_2026-04-19.md`
- `docs/83_WF3_User_Administration_Lite_Master_Plan_2026-04-19.md`
- `docs/84_WF3_A_User_Lifecycle_and_Provisioning_Core_Execution_Pack_2026-04-19.md`
- `docs/85_WF3_A_User_Lifecycle_and_Provisioning_Core_Implementation_Execution_Pack_2026-04-19.md`
- `docs/86_WF3_B_User_Administration_Surface_Execution_Pack_2026-04-19.md`
- `docs/87_WF3_C_Account_Safety_and_Access_Guard_Rails_Execution_Pack_2026-04-19.md`
- `docs/88_WF3_D_Quality_Hardening_and_Documentation_Execution_Pack_2026-04-19.md`
- `docs/89_WF4_Operational_History_and_Run_Archive_Master_Plan_2026-04-19.md`
- `docs/90_WF4_A_Checklist_Run_Archive_Core_Execution_Pack_2026-04-19.md`
- `docs/46_R4_Dashboard_Assembly_Extraction_Execution_Pack_2026-04-16.md`
