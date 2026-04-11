# Refactor Execution Pack

Date: 2026-04-11
Project: Daily Ops Command Center
Purpose: translate the master refactor plan into execution-ready work items with order, dependencies, verification, and closure rules
References:

- [27_Full_Codebase_Audit_2026-04-11.md](./27_Full_Codebase_Audit_2026-04-11.md)
- [28_Master_Refactor_Plan_2026-04-11.md](./28_Master_Refactor_Plan_2026-04-11.md)

## 1. Execution Rules

- execute in phase order unless an explicit dependency exception is documented
- every task must update code, tests, and canonical docs when contract truth changes
- no feature expansion is allowed during this program
- no phase is complete until lint and automated tests pass

## 2. Phase-by-Phase Task List

## Phase 0. Governance Lock

Goal:

- establish the operating rules of the refactor program

Tasks:

### P0-T1. Record the formal refactor kickoff

Status:

- completed in this round

Outputs:

- decision-log entry for the master refactor program

Primary files:

- `docs/05_Decision_Log_v1.3.md`

Verification:

- decision log explicitly states refactor-only governance and no scope expansion

### P0-T2. Bind roadmap and execution artifacts to the active repo state

Status:

- completed in this round

Outputs:

- debt roadmap references the active refactor program
- execution pack exists as the task authority for implementation rounds

Primary files:

- `docs/26_Architecture_Debt_Roadmap_2026-04-11.md`
- `docs/29_Refactor_Execution_Pack_2026-04-11.md`

Verification:

- a future implementation round can identify the correct task order without re-reading the full audit

Exit gate:

- the team has one planning chain: audit → master plan → execution pack

## Phase 1. Domain Truth Resolution

Goal:

- remove semantic ambiguity from checklist execution and canonical vocabulary

Tasks:

### P1-T1. Lock the singular daily checklist execution truth

Status:

- completed in this round

Outputs:

- docs and UI copy now state that daily execution is singular and global
- scope is explicitly treated as classification metadata

Primary files:

- `docs/00_Project_Lock_v1.1.md`
- `docs/02_System_Spec_v0.3.md`
- `docs/04_Current_State_v1.3.md`
- `docs/05_Decision_Log_v1.3.md`
- `docs/06_Data_Definition_v1.2.md`
- `docs/22_Architecture_Boundary_and_Execution_Standards_2026-04-11.md`
- `docs/24_Domain_Normalization_Design_2026-04-11.md`
- `resources/views/livewire/admin/checklist-templates/index.blade.php`
- `resources/views/livewire/admin/checklist-templates/manage.blade.php`
- `resources/views/livewire/staff/checklists/daily-run.blade.php`

Verification:

- no canonical document implies that `scope` already creates multiple runtime checklist flows
- admin template UI does not overstate current runtime capability

### P1-T2. Expand domain enum usage in core code paths

Status:

- completed in this round

Outputs:

- role and status logic uses domain enums in more core locations
- seed and fixture defaults now align better with canonical values

Primary files:

- `app/Models/User.php`
- `app/Http/Middleware/EnsureUserHasRole.php`
- `app/Livewire/Management/Incidents/Show.php`
- `database/seeders/DatabaseSeeder.php`
- `database/factories/UserFactory.php`

Verification:

- critical paths no longer rely on avoidable raw-string comparisons where canonical enums already exist

### P1-T3. Identify the remaining raw-string hot spots for Phase 2 and beyond

Status:

- deferred to next round

Remaining hotspots:

- route middleware role parameters
- migrations storing unconstrained string values
- many feature tests still query by string role and status
- some views still render raw domain values directly

Exit gate:

- singular checklist truth is now explicit in code and docs
- enum adoption is materially improved in active code paths

## Phase 2. Persistence Invariant Hardening

Goal:

- harden the repository against invalid business state

Dependencies:

- Phase 1 complete

Tasks:

### P2-T1. Decide the persistence strategy for `scope`

Decision required:

- keep `scope` nullable or make it required

Recommended option:

- make it required for new and existing templates if no legitimate null case remains

Why:

- the current admin flow requires scope already
- a nullable column now weakens the contract unnecessarily

Current decision:

- `scope` remains required at the application contract level
- schema nullability is not being rebuilt in this round because the current supported baseline is SQLite and the broader table-rebuild cost is not justified yet

Status:

- completed as a strategic decision

### P2-T2. Harden canonical value constraints

Targets:

- `users.role`
- `incidents.status`
- `checklist_run_items.result`
- `checklist_templates.scope`

Recommended output:

- DB constraints where safely supported
- otherwise explicit migration comments plus write-path guards and tests

Current decision:

- full DB CHECK coverage for every canonical string family is deferred
- domain/application enforcement remains the active strategy for role, scope, status families outside the most dangerous template invariants

Status:

- partially completed as a strategy decision

### P2-T3. Harden the "one active template" invariant

Targets:

- schema and application write path

Recommended output:

- transactional write protection
- explicit documentation for why the invariant is global

Status:

- completed in this round

Outputs:

- `SaveChecklistTemplate` now retires other active templates before saving the current active template inside the transaction
- migration adds DB-backed hardening for:
  - unique checklist template title
  - single global active template on SQLite baseline
- regression tests assert duplicate-title and duplicate-active writes fail

Primary files:

- `app/Application/ChecklistTemplates/Actions/SaveChecklistTemplate.php`
- `database/migrations/2026_04_11_000008_harden_checklist_template_invariants.php`
- `tests/Feature/AdminSurfaceBoundaryTest.php`

Verification:

- invalid state cannot be produced through normal supported write paths

## Phase 3. Authorization and Route Consolidation

Goal:

- remove remaining transitional behavior and tighten route truth

Tasks:

### P3-T1. Remove legacy `/admin/*` compatibility routes

Prerequisite:

- template management in `/templates` is stable and accepted

Status:

- completed in this round

Outputs:

- legacy `/admin/*` checklist-template redirects removed from the supported route contract
- route composition now exposes one canonical admin template route family only
- regression tests assert retired admin URLs return `404`

Primary files:

- `routes/web.php`
- `tests/Feature/AdminSurfaceBoundaryTest.php`
- `docs/05_Decision_Log_v1.3.md`

Verification:

- there is one route family and one route truth for checklist template administration

### P3-T2. Reassess route-level versus policy-level authorization

Recommended option:

- keep middleware for coarse role access
- add policy logic only if template actions require object-level decisions

Status:

- completed in this round

Outputs:

- checklist template administration remains admin-only behind route middleware
- no object-level policy layer added because the current use case has no per-record authorization split
- role-truth usage moved further toward `UserRole` enum-backed values across tests and supporting helpers

Primary files:

- `app/Http/Middleware/EnsureUserHasRole.php`
- `app/Domain/Access/Enums/UserRole.php`
- `app/Models/User.php`
- `tests/Feature/**`

Verification:

- authorization story remains single-layered and predictable for current admin template flows

### P3-T3. Centralize remaining role-truth helpers

Targets:

- middleware
- route composition
- tests

Status:

- materially reduced in this round

Verification:

- there is one route family and one authorization story for admin template work

## Phase 4. Frontend Architecture Consolidation

Goal:

- unify not only styling but also authoring model and component placement

Tasks:

### P4-T1. Decide the future of settings pages

Decision required:

- keep Volt-style anonymous component pages
- or migrate settings to explicit Livewire classes

Recommended option:

- migrate settings to explicit Livewire classes gradually

Current-cycle decision:

- defer the class migration and keep page-owned settings for now
- prioritize shared app-owned frontend contracts first

### P4-T2. Modularize the CSS contract

Targets:

- shared tokens
- shell styles
- form/control styles
- auth/settings styles

Status:

- completed in this round

Outputs:

- `resources/css/app.css` reduced to an entrypoint plus imports
- app-owned CSS contract split into dedicated modules under `resources/css/app/**`
- settings/auth surfaces restyled against the modular contract instead of accumulating more one-off rules

Primary files:

- `resources/css/app.css`
- `resources/css/app/tokens.css`
- `resources/css/app/base.css`
- `resources/css/app/ops.css`
- `resources/css/app/auth.css`
- `resources/css/app/settings.css`

### P4-T3. Reduce Flux-specific styling leakage

Targets:

- app-owned classes should define the contract
- Flux should behave more like a primitive library than a parallel system

Status:

- materially reduced in this round

Outputs:

- settings 2FA, recovery-code, and delete-account flows now use more app-owned settings classes
- modal copy, button rows, and supporting text follow project-owned presentation patterns more consistently

Primary files:

- `resources/css/app/settings.css`
- `resources/views/pages/settings/⚡two-factor-setup-modal.blade.php`
- `resources/views/pages/settings/two-factor/⚡recovery-codes.blade.php`
- `resources/views/pages/settings/⚡delete-user-modal.blade.php`
- `resources/views/pages/settings/⚡delete-user-form.blade.php`

Verification:

- auth/settings surfaces still work with Flux primitives while the visual contract is increasingly controlled by app-owned classes

Verification:

- a new developer can predict where a new page belongs and which frontend contract to follow

## Phase 5. Fixture and Seed Separation

Goal:

- separate demo narrative from automated test correctness

Tasks:

### P5-T1. Reduce seeded-title coupling in tests

Status:

- completed in this round

Outputs:

- critical feature/application tests no longer rely on seeded demo titles, seeded emails, or full `DatabaseSeeder` bootstrap state
- explicit scenario setup now owns the state that tests actually need

Primary files:

- `tests/Feature/AuthenticationPolicyTest.php`
- `tests/Feature/Application/CreateIncidentActionTest.php`
- `tests/Feature/Application/InitializeDailyRunActionTest.php`
- `tests/Feature/Application/SubmitDailyRunActionTest.php`
- `tests/Feature/NavigationRegressionTest.php`
- `tests/Feature/ChecklistDailyRunTest.php`
- `tests/Feature/AdminSurfaceBoundaryTest.php`

### P5-T2. Introduce purpose-built fixtures or helper builders

Status:

- completed in this round

Outputs:

- project-owned factories exist for checklist templates, checklist items, checklist runs, checklist run items, incidents, and incident activities
- reusable application scenario helper exists for common role/template/run/incident setup

Primary files:

- `database/factories/UserFactory.php`
- `database/factories/ChecklistTemplateFactory.php`
- `database/factories/ChecklistItemFactory.php`
- `database/factories/ChecklistRunFactory.php`
- `database/factories/ChecklistRunItemFactory.php`
- `database/factories/IncidentFactory.php`
- `database/factories/IncidentActivityFactory.php`
- `tests/CreatesApplicationScenarios.php`
- `tests/Pest.php`

### P5-T3. Keep demo seed data useful but non-authoritative for most tests

Status:

- materially reduced in this round

Outputs:

- canonical docs now distinguish demo seed narrative from automated test correctness
- seeded demo data remains useful for bootstrap and manual demo flows without acting as the default source of truth for behavior tests

Primary files:

- `docs/05_Decision_Log_v1.3.md`
- `docs/06_Data_Definition_v1.2.md`
- `docs/26_Architecture_Debt_Roadmap_2026-04-11.md`

Verification:

- changing demo records should not ripple across unrelated behavior tests
- factories and scenario helpers can build core application state without calling `DatabaseSeeder`

## Phase 6. Documentation Truth Partitioning

Goal:

- reduce interpretation cost for active engineering truth

Tasks:

### P6-T1. Remove bootstrap residue

Targets:

- `.env.example`
- `README.md`

Status:

- materially reduced in this round

Outputs:

- `.env.example` no longer advertises starter-kit app identity
- `README.md` now distinguishes demo bootstrap from automated test correctness

### P6-T2. Clarify historical vs active decisions

Targets:

- append-only decision log
- current-state summary
- architecture standards

Status:

- materially reduced in this round

Outputs:

- canonical docs now distinguish active refactor state from already-closed foundation remediation
- data-definition guidance now separates demo narrative from test-fixture truth

Verification:

- a contributor can bootstrap and understand the current baseline without reading historical decisions defensively

## Phase 7. Browser-Level Regression Safety

Goal:

- protect the highest-value user-facing flows during future refactor rounds

Tasks:

### P7-T1. Select the browser test tool

Recommended option:

- start with the lightest reliable stack for a narrow smoke pack

Status:

- completed in this round

Outputs:

- `Pest Browser + Playwright` selected as the repository browser-regression stack
- browser configuration added to Pest bootstrap
- CI workflow includes a dedicated browser job

Primary files:

- `composer.json`
- `package.json`
- `tests/Pest.php`
- `.github/workflows/tests.yml`

### P7-T2. Add the first critical-path browser suite

Suggested flows:

- home
- login
- dashboard
- incidents index
- incident detail
- checklist templates list and edit
- settings navigation

Status:

- partially completed in this round

Outputs:

- browser smoke suite covers guest-facing home/login, admin login-to-templates flow, and staff login-to-daily-checklist flow
- screenshots directory is ignored from git

Primary files:

- `tests/Browser/SmokeTest.php`
- `.gitignore`

Verification:

- the suite is stable enough to run in CI and small enough to remain trusted
- local execution still requires Playwright host libraries; current WSL host is not yet fully provisioned for headless browser runtime

## 3. Current Progress Snapshot

Completed:

- Phase 0
- Phase 1

Partially completed:

- Phase 2
- Phase 3
- Phase 4
- Phase 5
- Phase 6
- Phase 7

Not started:

- Phase 7

## 4. Immediate Next Step

The next correct implementation step is:

- stabilize Phase 7 execution environments and then close the remaining selective hardening/documentation residue

That means the next execution round should focus on:

1. proving the browser smoke suite on CI
2. deciding whether local browser execution will be standardized or documented as optional
3. closing the remaining deferred selective-hardening and documentation residue without reopening feature scope

## 5. Closure Standard

This execution pack is successful only if future rounds continue to close phases rather than reopening ambiguity.

The project must not drift back into:

- hidden transitional routes
- dual truth between docs and code
- surface polish without contract cleanup
