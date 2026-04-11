# Master Refactor Plan

Date: 2026-04-11
Project: Daily Ops Command Center
Purpose: convert the full codebase audit into a phased, execution-ready refactor strategy for a high-standard, maintainable, single-surface application baseline
Reference input: [27_Full_Codebase_Audit_2026-04-11.md](./27_Full_Codebase_Audit_2026-04-11.md)
Mode: planning only

## 1. Executive Decision

This repository should not be rewritten from scratch.

The correct strategy is:

- preserve the working Laravel monolith
- preserve the application-layer extraction that already exists
- refactor by contract, not by surface cosmetics
- unify the product around one coherent operational truth

The refactor must be driven by five workstreams:

- domain contract cleanup
- persistence invariant hardening
- frontend architecture consolidation
- documentation truth partitioning
- regression safety expansion

## 2. Refactor Objectives

The program aims to leave the repository with:

- one coherent product model
- one coherent frontend presentation system
- one explicit persistence contract
- one explicit documentation truth hierarchy
- one regression safety baseline suitable for long-term iteration

The program does not aim to:

- add major new product scope
- redesign the domain beyond what current product requirements justify
- introduce infrastructure patterns without demonstrated value

## 3. Global Execution Rules

The full program must obey these rules.

### Rule A. No scope expansion during refactor

Do not add:

- assignment workflows
- notifications
- analytics
- extra admin modules
- approval systems

unless the product lock and decision log are updated first.

### Rule B. Every refactor phase must end with truth alignment

Each phase must update, as applicable:

- code
- tests
- canonical docs
- seed and fixture assumptions

No phase is done if only code changed.

### Rule C. No "temporary" dual-truth state without an explicit sunset condition

If a transition layer is introduced:

- state what is temporary
- define what removes it
- define its target removal phase

### Rule D. Prefer contract consolidation before UI polish

Do not spend time on visual polish before:

- domain truth
- database truth
- route truth
- authorization truth

are aligned.

## 4. Workstream Breakdown

### Workstream W1. Domain Contract Refactor

Purpose:

- decide the real business truth of checklist execution, scope, role semantics, incident state, and template lifecycle

Primary outputs:

- consistent domain vocabulary
- reduced raw-string duplication
- explicit invariants

### Workstream W2. Persistence Contract Hardening

Purpose:

- move critical invariants from "UI path assumptions" into persistence-level guarantees where appropriate

Primary outputs:

- hardened schema
- safer writes
- lower silent drift risk

### Workstream W3. Frontend Architecture Consolidation

Purpose:

- move from "visually closer" to "structurally coherent"

Primary outputs:

- unified authoring model
- clearer shell ownership
- lower presentation drift

### Workstream W4. Documentation Truth Partitioning

Purpose:

- make it impossible to confuse historical decision context with current engineering contract

Primary outputs:

- cleaner active docs
- better change discipline

### Workstream W5. Regression Safety Expansion

Purpose:

- protect the exact surfaces most likely to regress during refactor

Primary outputs:

- stronger route, behavior, and browser-level confidence

## 5. Dependency Map

The program should follow this dependency order:

1. product and domain truth
2. persistence truth
3. authorization and route truth
4. frontend architecture truth
5. docs and regression completion

Why:

- if domain truth is unclear, schema changes will be wrong
- if schema truth is weak, UI refactors can still sit on unstable state
- if authorization and route truth are unstable, navigation and UX cleanup will drift
- if frontend is polished before contract cleanup, the team will decorate contradictions

## 6. Phase Plan

## Phase 0. Refactor Governance Lock

Status:

- must happen before execution starts

Goal:

- freeze architectural ambiguity and create the operating rules for the refactor program

Tasks:

1. Declare a refactor-only period for foundation work.
2. Confirm the active canonical doc set.
3. Mark transitional routes and legacy behavior as temporary.
4. Define merge rules for refactor branches.
5. Require every phase to update docs and tests before closure.

Deliverables:

- decision log entry confirming the refactor program start
- updated roadmap note indicating active workstreams

Acceptance criteria:

- no new feature work lands without checking this plan
- active and historical docs are clearly distinguished to the team

Risks if skipped:

- refactor turns into opportunistic cleanup
- documentation and code drift reappears immediately

## Phase 1. Domain Truth Resolution

Priority:

- highest

Goal:

- resolve the core domain contradictions before touching schema and large UI contracts

Primary decision gate:

- is `ChecklistScope` real operational workflow state or only administrative metadata?

Recommended decision:

- for current product scope, keep singular daily execution as the source of truth
- treat scope as template classification unless and until multiple operational checklist flows are explicitly added to the product lock

Why this is the best current option:

- it matches current runtime behavior
- it avoids inventing fake multi-flow complexity
- it lowers refactor cost
- it keeps future scope expansion possible without lying about current capability

Tasks:

1. Define the authoritative checklist execution model in code and docs.
2. Define the template lifecycle model:
   - active
   - retired
   - history-protected
3. Define whether removing historical checklist items is forbidden or versioned.
4. Finish the canonical vocabulary for:
   - role
   - checklist scope
   - checklist result
   - incident category
   - incident severity
   - incident status
5. Replace remaining raw-string logic in domain-sensitive code with enum-backed or canonical helpers where practical.

Primary files likely affected:

- `app/Domain/**`
- `app/Application/**`
- `app/Models/User.php`
- `app/Livewire/**`
- `docs/04_Current_State_v1.3.md`
- `docs/05_Decision_Log_v1.3.md`
- `docs/06_Data_Definition_v1.2.md`
- `docs/22_Architecture_Boundary_and_Execution_Standards_2026-04-11.md`
- `docs/24_Domain_Normalization_Design_2026-04-11.md`

Acceptance criteria:

- checklist scope truth is no longer ambiguous
- no critical business workflow relies on undocumented semantics
- enum usage is materially expanded across business-sensitive code

Exit gate:

- a new developer can answer "how many active daily checklist flows can exist?" from docs and code without interpretation

## Phase 2. Persistence Invariant Hardening

Priority:

- highest, immediately after Phase 1

Goal:

- encode the most important domain truth into the database and write-path rules

Tasks:

1. Add schema-level protection for canonical values where feasible.
2. Evaluate database `check` constraints or safe application-level migration guards for:
   - `users.role`
   - `incidents.status`
   - `incidents.severity`
   - `checklist_run_items.result`
   - `checklist_templates.scope`
3. Define the persistence strategy for "single active template":
   - partial unique index if supported by target DB strategy
   - or application-enforced invariant with explicit transactional guard and documented portability reason
4. Decide whether `scope` should remain nullable.
5. Review foreign keys and delete semantics for long-term history protection.
6. Separate write-policy assumptions from seeder convenience logic.

Recommended decision:

- keep Eloquent as persistence boundary
- do not add repository pattern
- use DB constraints and focused write actions instead

Why this is the best option:

- repository abstraction would add indirection without solving the real problem
- the real problem is invariant enforcement, not query organization

Primary files likely affected:

- `database/migrations/**`
- `app/Application/**`
- `app/Models/**`
- `database/seeders/DatabaseSeeder.php`
- tests covering invalid state transitions

Acceptance criteria:

- invalid business-state values cannot be inserted through normal migration-supported paths
- the "active template" invariant is explicitly hardened and documented
- historical run/item integrity is intentionally protected, not incidentally protected

Exit gate:

- persistence truth matches domain truth for critical entities

## Phase 3. Authorization and Route Contract Consolidation

Priority:

- high

Goal:

- make route semantics, role semantics, and authorization semantics consistent and scalable

Tasks:

1. Replace remaining raw role checks with centralized helpers or enum-based checks.
2. Decide whether route middleware remains sufficient or whether policy classes should begin for admin template actions.
3. Remove transitional `/admin/*` redirects once the consolidated `/templates` surface is proven stable.
4. Review `landingRouteName()` and route ownership for long-term maintainability.
5. Audit settings routes and verified-account rules for consistency.

Recommended decision:

- keep route middleware for coarse access
- introduce policy classes only where object-level authorization adds real value

Why this is the best option:

- current feature set does not justify wholesale policy conversion
- but admin template ownership may justify policy introduction if permissions expand

Primary files likely affected:

- `routes/web.php`
- `routes/settings.php`
- `app/Http/Middleware/**`
- `app/Models/User.php`
- `tests/Feature/AdminSurfaceBoundaryTest.php`
- `tests/Feature/NavigationRegressionTest.php`

Acceptance criteria:

- no legacy admin surface semantics survive unintentionally
- no role logic is duplicated in conflicting ways
- route behavior communicates the actual product model clearly

Exit gate:

- there is one clear answer to "where does admin work happen?" and one clear route family for it

## Phase 4. Frontend Architecture Consolidation

Priority:

- high

Goal:

- move from a visually improved interface to a structurally unified frontend system

Tasks:

1. Define the canonical page-authoring pattern for all first-party product screens.
2. Decide whether settings pages remain Volt-style anonymous components or are migrated to explicit Livewire classes.
3. Consolidate auth/settings/app shell rules into a single frontend architecture contract.
4. Split CSS into maintainable modules or sections if the stylesheet continues growing.
5. Standardize shared view primitives:
   - cards
   - forms
   - tables
   - buttons
   - alerts
   - settings panels
6. Reduce direct Flux-specific styling leakage where app-owned classes should be authoritative.
7. Normalize page-level copy structure and heading hierarchy.

Recommended decision:

- migrate settings pages toward explicit Livewire classes over time
- keep Flux as a UI primitive library, not as a competing page-ownership model

Why this is the best option:

- it matches the rest of the application direction
- it improves navigability and refactor ergonomics
- it reduces "special case" mental overhead

Primary files likely affected:

- `resources/views/layouts/**`
- `resources/views/pages/auth/**`
- `resources/views/pages/settings/**`
- `app/Livewire/**`
- `resources/css/app.css`
- possibly new `resources/css/components/**` or a similar structure if adopted

Acceptance criteria:

- first-party product screens follow one explicit authoring philosophy
- the CSS contract is easier to navigate and evolve
- settings/auth no longer feel like a structurally separate subsystem

Exit gate:

- a developer can predict where to add a new screen and how it should be built without guessing

## Phase 5. Fixture, Seed, and Test Contract Separation

Priority:

- medium-high

Goal:

- stop mixing demo data, test assumptions, and product truth into one fragile layer

Tasks:

1. Separate demo seed intent from test fixture intent.
2. Reduce tests that depend on exact seeded titles unless they are deliberately narrative tests.
3. Introduce factories or focused setup helpers for domain-specific states.
4. Keep a demo seed, but make it explicitly a demo dataset rather than a hidden system dependency.
5. Review `firstOrCreate` usage in seeding and decide whether idempotence is helping or hiding drift.

Recommended decision:

- keep a demo seeder
- move most feature tests toward explicit fixture setup rather than relying on the demo seed

Why this is the best option:

- it preserves demo convenience
- it improves test determinism
- it reduces the chance that local data drift becomes invisible

Primary files likely affected:

- `database/seeders/DatabaseSeeder.php`
- `database/factories/**`
- `tests/Feature/**`

Acceptance criteria:

- test setup is deterministic and intentional
- demo data remains useful without acting as a hidden production contract

Exit gate:

- changing demo copy or demo records does not accidentally break unrelated tests

## Phase 6. Documentation Truth Partitioning

Priority:

- medium-high

Goal:

- make current engineering truth explicit and historical context safely secondary

Tasks:

1. Define the difference between:
   - canonical active contract
   - historical decision history
   - audit and planning artifacts
2. Update `README.md` to be onboarding-accurate and product-accurate.
3. Remove starter residue such as `APP_NAME=Laravel` from `.env.example`.
4. Decide whether `docs/05_Decision_Log_v1.3.md` should stay append-only or be paired with a shorter "current decisions" contract file.
5. Update all canonical docs to reference the same present-day architecture truth.

Recommended decision:

- keep the append-only decision log
- but pair it with stronger "active contract" docs and explicit supersession language

Why this is the best option:

- history remains available
- active truth becomes easier to consume
- the team avoids losing rationale while reducing ambiguity

Primary files likely affected:

- `.env.example`
- `README.md`
- `docs/04_Current_State_v1.3.md`
- `docs/05_Decision_Log_v1.3.md`
- `docs/22_Architecture_Boundary_and_Execution_Standards_2026-04-11.md`
- `docs/26_Architecture_Debt_Roadmap_2026-04-11.md`

Acceptance criteria:

- active engineering truth can be found quickly
- historical decisions do not conflict with current operating assumptions

Exit gate:

- a new contributor can bootstrap and understand the system without interpreting contradictory documents

## Phase 7. Browser-Level Regression Safety

Priority:

- medium

Goal:

- add protection for the exact user-visible surfaces that historically drifted most

Tasks:

1. Define a small browser-level regression pack for the highest-value flows:
   - home
   - login
   - dashboard
   - incidents index
   - incident detail
   - checklist templates list and edit
   - settings navigation
2. Decide testing technology:
   - Laravel Dusk if environment control is acceptable
   - Playwright if cross-browser or stronger frontend validation is desired
3. Keep the suite small and reliable.
4. Add only flows whose regression would be expensive to discover manually.

Recommended decision:

- start with a narrow browser suite, not a broad one

Why this is the best option:

- the project needs confidence, not an automation burden explosion
- broad UI automation too early will become flaky and get ignored

Primary files likely affected:

- test harness additions
- CI workflow updates
- documentation for local execution

Acceptance criteria:

- major surface regressions can be caught before merge
- the suite is fast enough that the team actually uses it

Exit gate:

- the repository has at least one user-visible regression layer beyond server-side feature tests

## 7. Recommended Phase Order

The recommended execution order is:

1. Phase 0: governance lock
2. Phase 1: domain truth resolution
3. Phase 2: persistence invariant hardening
4. Phase 3: authorization and route consolidation
5. Phase 4: frontend architecture consolidation
6. Phase 5: fixture and seed separation
7. Phase 6: documentation truth partitioning
8. Phase 7: browser-level regression safety

This order is preferred because:

- it resolves semantic contradictions before structural edits
- it reduces the chance of refactoring the wrong frontend shape
- it protects the refactor with tests only after the architecture direction is stable enough to encode

## 8. Suggested Iteration Size

Do not execute this plan as one giant branch.

Recommended cadence:

- one branch per phase
- or one branch per major workstream slice within a phase

Preferred branch shape:

- Phase 1 branch
- Phase 2 branch
- Phase 3 branch
- Phase 4a auth/settings consolidation
- Phase 4b CSS contract modularization
- Phase 5 branch
- Phase 6 branch
- Phase 7 branch

## 9. Risk Register

### Risk R1. Over-refactoring the checklist domain

Risk:

- the team may accidentally introduce multiple operational checklist flows before product scope justifies them

Mitigation:

- lock singular execution truth unless the product lock changes

### Risk R2. Database hardening may be implemented in a DB-specific way that conflicts with future portability

Risk:

- constraint strategy may overfit SQLite or underfit future MySQL/Postgres needs

Mitigation:

- document constraint portability decisions explicitly

### Risk R3. Frontend unification could drift back into cosmetic work only

Risk:

- the team may polish CSS while leaving authoring-model fragmentation untouched

Mitigation:

- require explicit frontend architecture decisions before visual refinement work

### Risk R4. Browser automation may become flaky and ignored

Risk:

- the team may add too much UI automation too soon

Mitigation:

- start with a minimal critical-path pack only

## 10. Definition of Success

The refactor program should be considered successful when:

- the checklist domain no longer says more than the runtime really supports
- critical business invariants are protected beyond UI happy paths
- the application is governed by one coherent frontend architecture
- docs cleanly separate active truth from historical context
- the repository has confidence at both server behavior and browser surface levels

## 11. Final Brutal Truth

The repository is good enough to build on, but not disciplined enough yet to scale casually.

The next major improvement will not come from "clean code" in the abstract.
It will come from making the system tell the truth in every layer:

- domain
- database
- routes
- UI
- docs
- tests

That is the correct standard for the next refactor program.
