# Foundation Remediation Plan

Date: 2026-04-11
Project: Daily Ops Command Center
Planning scope: This document is the Plan phase that follows the audit in [17_Codebase_Audit_Report_2026-04-10.md](./17_Codebase_Audit_Report_2026-04-10.md). It defines the target direction, decision rationale, sequencing, workstreams, acceptance gates, and execution strategy. It intentionally does not implement changes.

## 1. Purpose

The purpose of this plan is to convert the audit findings into an execution-ready remediation strategy that is:

- robust
- scalable
- maintainable
- aligned with strict Software Engineering principles
- compatible with a senior-engineer standard of planning and delivery

This plan is not a hotfix plan.
This plan is not a patch list.
This plan is a foundation-rebuild roadmap for a codebase that currently works, but is structurally inconsistent.

## 2. Inputs

Primary input:

- [17_Codebase_Audit_Report_2026-04-10.md](./17_Codebase_Audit_Report_2026-04-10.md)

Secondary contextual inputs:

- [02_System_Spec_v0.3.md](./02_System_Spec_v0.3.md)
- [06_Data_Definition_v1.2.md](./06_Data_Definition_v1.2.md)
- [05_Decision_Log_v1.3.md](./05_Decision_Log_v1.3.md)
- [11_Implementation_Task_List_v1.0.md](./11_Implementation_Task_List_v1.0.md)

## 3. Planning Principles

The following principles govern all subsequent work:

### P1. Fix root causes before expanding features

No new product scope should be added until platform, architecture, and repository hygiene are stabilized.

### P2. Establish one source of truth per concern

Each of the following must have a canonical owner:

- platform baseline
- route ownership
- layout ownership
- domain constants
- business workflow logic
- generated assets policy

### P3. Reduce paradigms, do not add more paradigms

The current codebase suffers from uncontrolled composition. The next phase must consolidate, not introduce a new framework, library, or architectural abstraction unless it removes more complexity than it adds.

### P4. Preserve working behavior while changing structure

Because the current app works for core flows, refactoring must be behavior-preserving wherever possible. The refactor should improve structure first and only then revisit behavior.

### P5. Make contracts explicit

The current failure pattern stems from implicit assumptions. The plan prioritizes explicit contracts in:

- CI
- composer metadata
- domain state definitions
- architectural boundaries
- repository policy

### P6. Sequence for leverage, not convenience

Work is ordered so that each phase lowers risk for the next phase. This is a leverage-first plan, not a file-by-file cleanup plan.

### P7. Stabilize runtime operability, not just code structure

A system is not structurally healthy if it compiles and tests locally but fails basic operational expectations such as:

- consistent environment bootstrap
- correct default `.env.example`
- attachment delivery working end-to-end
- storage link assumptions being documented and reproducible

The plan must therefore treat operability as part of the foundation, not as a postscript.

### P8. Treat authorization and account lifecycle as first-class architecture

Role checks alone are not a complete authorization model. The remediation must explicitly account for:

- inactive users
- account lifecycle rules
- whether email verification is required for operations
- whether self-registration is allowed, disallowed, or intentionally unsupported

### P9. Protect data during structural refactoring

Any phase that changes schema shape, domain values, enums, or persistence ownership must define how existing data is preserved, transformed, or audited. Data migration safety is part of engineering quality, not a later concern.

## 4. Target State

The desired post-remediation state is:

1. A single, explicit platform contract.
2. A small number of intentionally chosen UI paradigms, each with clear ownership.
3. Business rules extracted from UI components into application-layer use cases.
4. Centralized domain definitions for statuses, categories, severities, and workflow rules.
5. One authoritative app shell and one authoritative auth/settings shell strategy.
6. A repository that stores source, not stale generated artifacts.
7. CI that reflects the true supported platform and acts as a reliable gate.
8. A test strategy that protects both behavior and architecture.

## 5. Explicit Architecture Decisions

This section makes the key choices that would otherwise remain ambiguous.

## Decision A. PHP baseline

### Options considered

Option A1: Preserve PHP 8.3 support

Pros:

- broader compatibility
- less aggressive baseline increase

Cons:

- conflicts with current resolved dependency graph
- would require package re-resolution or downgrades
- creates churn without strategic value
- delays stabilization

Option A2: Standardize on PHP 8.4+

Pros:

- matches current local runtime
- matches current lock-file reality
- removes immediate CI contradiction
- simpler governance and lower ambiguity

Cons:

- tighter minimum platform requirement
- requires explicit communication and CI normalization

### Selected decision

Choose Option A2: standardize on PHP 8.4 as the minimum supported version.

### Rationale

This is the smallest decision that restores truth to the system. The repository already behaves like a PHP 8.4 project. The correct engineering move is to align declared support with actual support, not to preserve a broader compatibility claim that is already false.

### Planning consequence

- update metadata, tooling, documentation, and CI to PHP 8.4 minimum
- do not spend planning capacity on backward compatibility to 8.3

## Decision B. UI ownership model

### Problem

The codebase currently mixes:

- custom Livewire class components
- Volt single-file components
- Flux/Pages conventions
- Filament admin panel

without a documented composition rule.

### Options considered

Option B1: Move everything into Filament

Pros:

- one admin-oriented framework
- built-in CRUD/admin patterns

Cons:

- poor fit for all user-facing operational workflows
- over-couples the whole product to Filament conventions
- creates migration cost without clear payoff

Option B2: Move everything into custom Livewire and remove Filament

Pros:

- one primary app stack
- maximum architectural control

Cons:

- loses leverage from Filament for low-value admin CRUD
- unnecessary rewrite of functioning template admin

Option B3: Use a split ownership model

- Filament only for internal admin/back-office CRUD
- custom Livewire for core operations workflows
- retain Volt temporarily only where it is already aligned to auth/settings, then decide whether to keep or migrate after shell consolidation

Pros:

- best fit by concern
- avoids needless rewrites
- reduces paradigm spread without burning working admin capabilities

Cons:

- not a single-framework solution
- requires strict boundaries and documentation

### Selected decision

Choose Option B3.

### Boundary definition

- Filament owns checklist template administration and future low-code admin CRUD only.
- Custom Livewire class components own the operations product flows:
  - checklist execution
  - incident reporting
  - incident review/update
  - dashboard
- Volt remains temporarily limited to auth/settings until shell and boundary consolidation are complete.

### Rationale

This is the most pragmatic and maintainable choice. It minimizes rewrites while still reducing uncontrolled composition.

## Decision C. Application architecture style

### Options considered

Option C1: Keep logic in Livewire components and just "clean up"

Pros:

- less initial work

Cons:

- does not solve architectural coupling
- locks future growth into presentation-driven orchestration

Option C2: Full Clean Architecture rewrite immediately

Pros:

- maximum purity

Cons:

- high risk
- expensive
- too disruptive for current project size

Option C3: Introduce a pragmatic layered architecture

- domain definitions in dedicated domain layer or support namespace
- application use cases/services for workflows
- Livewire components become thin presenters/controllers
- Eloquent remains persistence mechanism

Pros:

- high structural payoff
- realistic for the current codebase
- preserves Laravel productivity

Cons:

- requires disciplined incremental refactor

### Selected decision

Choose Option C3.

### Target layering

- Domain layer:
  - enums/value definitions
  - workflow invariants
  - transition rules
- Application layer:
  - use-case services/actions
  - orchestration
  - transaction boundaries
- Presentation layer:
  - Livewire/Volt/Blade/Filament resources
- Infrastructure layer:
  - Eloquent models
  - storage
  - framework integrations

### Rationale

This is the strongest maintainable option that remains proportionate to the project size.

## Decision D. Layout and shell ownership

### Problem

There is no single authoritative application shell. Navigation and user-menu concerns are duplicated across multiple files.

### Options considered

Option D1: Keep current layered shell and reduce duplication ad hoc

Cons:

- ambiguity remains
- duplication risk remains

Option D2: Define one authoritative app shell and one authoritative auth/settings shell

Pros:

- clean ownership
- lower drift
- easier navigation changes

Cons:

- requires intentional consolidation work

### Selected decision

Choose Option D2.

### Target shell policy

- one authoritative application shell for authenticated ops pages
- one authoritative auth/settings shell strategy
- no duplicate full-document app shells
- navigation rendered from reusable role-aware navigation definitions, not repeated markup blocks

## Decision E. Domain constants strategy

### Options considered

Option E1: Continue string literals everywhere

Cons:

- unacceptable drift risk

Option E2: Centralize into config arrays

Pros:

- easy

Cons:

- still weakly typed
- poor domain expression

Option E3: Use typed PHP enums or equivalent canonical domain definitions

Pros:

- stronger contracts
- shared by validation, queries, views, and tests
- better refactor safety

Cons:

- moderate initial migration effort

### Selected decision

Choose Option E3.

### Target canonical definitions

- `IncidentStatus`
- `IncidentSeverity`
- `IncidentCategory`
- `ChecklistResult`
- possibly `UserRole`

## Decision F. Repository asset policy

### Problem

The repo contains source and generated/vendor-published public assets without a clear policy.

### Options considered

Option F1: Keep all current public assets committed

Pros:

- fewer immediate deployment questions

Cons:

- stale artifact risk
- noisy diffs
- harder upgrades

Option F2: Source-only repository with documented generation steps

Pros:

- cleaner repo
- smaller diffs
- clearer source of truth

Cons:

- requires setup/deploy scripts to be explicit and reliable

### Selected decision

Choose Option F2.

### Policy

- source files are tracked
- generated build output is not tracked
- published vendor assets are tracked only if truly required and regeneration is not reliable
- current tracked Filament public assets must be reviewed and likely removed from source control in a controlled migration phase

## Decision G. Settings/auth strategy

### Options considered

Option G1: Keep Volt indefinitely for settings/auth

Pros:

- avoids immediate migration work

Cons:

- preserves mixed paradigms

Option G2: Immediately migrate settings/auth away from Volt into class-based Livewire

Pros:

- stronger unification

Cons:

- high rewrite cost now

Option G3: Freeze settings/auth feature scope, keep Volt temporarily, re-evaluate after shell and application-layer consolidation

Pros:

- reduces change surface
- avoids combining too many refactors at once
- makes sequencing safer

Cons:

- temporary mixed state remains

### Selected decision

Choose Option G3.

### Rationale

The settings/auth area is not the current root cause of operational instability. It is a composition concern, but not the first lever. It should be frozen, isolated, and revisited after the higher-leverage work is done.

## Decision H. Environment and bootstrap contract

### Problem

The current environment story is internally inconsistent:

- [README.md](../README.md) says local development uses SQLite
- [.env.example](../.env.example) defaults to MySQL and a project-specific database name
- runtime currently uses SQLite locally
- attachment links assume `public/storage`, but `php artisan about` reports `public/storage` as not linked

### Options considered

Option H1: Keep multiple local bootstrap stories and document them loosely

Cons:

- causes onboarding confusion
- makes setup nondeterministic
- increases support burden

Option H2: Define one canonical local bootstrap profile and treat alternatives as advanced/optional

Pros:

- deterministic setup
- easier CI parity
- lower onboarding cost

Cons:

- requires choosing one default

### Selected decision

Choose Option H2.

### Canonical contract

- local default profile: SQLite
- `.env.example` must reflect the canonical local profile
- README and setup scripts must align with `.env.example`
- attachment/storage behavior must include explicit `storage:link` expectations where required

### Rationale

A foundation plan that ignores bootstrap truth is incomplete. Environment mismatch is a real root-cause class, not documentation noise.

## Decision I. Authorization and account lifecycle policy

### Problem

The current codebase has role checks, but important account constraints are not systematically enforced:

- `is_active` exists in schema and model but is not enforced in authentication or request access
- email verification is enabled in Fortify, but only some settings routes require `verified`
- operational routes require `auth`, not `verified`
- public registration policy was previously ambiguous because registration residue existed without an enabled public registration route

### Options considered

Option I1: Treat current role middleware as sufficient

Cons:

- ignores inactive-user risk
- leaves authorization model under-specified

Option I2: Add a dedicated authorization and account-lifecycle design track

Pros:

- clarifies who may authenticate
- clarifies who may operate
- clarifies whether verification matters
- clarifies whether self-registration is intentionally unsupported

Cons:

- adds planning scope

### Selected decision

Choose Option I2.

### Planning consequence

The remediation plan must explicitly define:

- inactive-user login/access policy
- email verification policy for operational routes
- whether public registration is permanently disabled
- where authorization rules live beyond route middleware

### Rationale

This was underrepresented in the first version of the plan and must be made explicit before execution.

## 6. Program Structure

The remediation program will run through six phases.

These phases are sequential by default. Some tasks inside a phase may be parallelized, but the phase gates themselves should be respected.

## Phase 0. Governance Freeze

### Goal

Stop structural entropy while planning and execution begin.

### Scope

- freeze new feature work
- freeze new framework/library additions
- freeze new generated/public artifact commits unless explicitly approved
- freeze new route/layout paradigm additions

### Deliverables

- documented freeze rules
- branch policy for remediation work
- definition of allowed vs disallowed changes during remediation

### Exit criteria

- team agreement that execution will not proceed in parallel with feature expansion

## Phase 1. Platform, Environment, and Build Truth

### Goal

Restore truthful and deterministic platform behavior.

### Why first

Nothing else should proceed while CI lies about supported runtime.

### Scope

- align `composer.json`, lock file, and CI to PHP 8.4 minimum
- normalize Actions workflows
- document required secrets and setup assumptions
- ensure local setup and CI setup use the same platform expectations
- align `.env.example`, README, and actual local bootstrap profile
- define runtime prerequisites such as `storage:link` and attachment delivery expectations

### Deliverables

- updated platform baseline
- deterministic CI matrix
- documented secret/setup prerequisites
- canonical local environment contract
- reproducible runtime bootstrap checklist
- green CI for baseline branch

### Non-goals

- major application refactors
- visual/UI rewrites

### Acceptance criteria

- CI passes on supported versions only
- `composer install` behavior is consistent with declared support
- no unsupported runtime remains in official docs or workflow files
- `.env.example`, README, and actual default runtime profile agree
- a clean clone can boot successfully without undocumented manual guesswork
- attachment delivery prerequisites are explicit and verified

### Risks

- hidden dependency assumptions
- setup discrepancies between local and CI

### Mitigation

- keep phase narrow
- verify with fresh install and CI reruns

## Phase 2. Architectural and Security Boundary Definition

### Goal

Define and codify system ownership boundaries before refactoring internals.

### Scope

- formalize which modules belong to:
  - Filament
  - custom Livewire
  - Volt
  - layouts/shell
- create directory and namespace conventions for application and domain layers
- define what "thin Livewire component" means in this codebase
- define authorization boundary rules:
  - role-based access
  - inactive-user handling
  - verification expectations
  - registration policy

### Deliverables

- architecture decision record for UI ownership
- target folder/namespace map
- coding conventions for use-case extraction
- shell ownership policy
- authorization and account-lifecycle policy

### Acceptance criteria

- no ambiguous ownership remains for new work
- future files can be classified by rule, not by guesswork
- access-control responsibilities are explicit rather than implicit

### Risks

- over-design
- defining abstractions too early
- mixing authentication concerns into unrelated workflow refactors

### Mitigation

- design only what is needed for the current domain

## Phase 3. Domain and Invariant Normalization

### Goal

Eliminate duplicated business vocabulary and migrate to canonical domain definitions.

### Scope

- introduce canonical enums/value definitions for:
  - roles
  - checklist results
  - incident statuses
  - incident severities
  - incident categories
- align validation, views, tests, and seeders to shared domain definitions
- document invariants explicitly
- define which invariants belong at:
  - schema level
  - domain level
  - application/use-case level
- define data transition strategy for moving from string-literal state to canonical definitions

### Deliverables

- centralized domain definitions
- reduced string literal duplication
- invariant map
- data transition map for affected schema/application changes

### Acceptance criteria

- no business-critical state values are scattered as unmanaged literals
- validation and UI options derive from canonical definitions
- invariant ownership is explicit
- planned data migrations exist for any persistence-impacting changes

### Risks

- migration churn across tests and views

### Mitigation

- perform state normalization before service extraction finishes to reduce double-work

## Phase 4. Application Workflow Extraction

### Goal

Move business logic out of presentation components and into application use cases.

### Priority workflows

1. checklist daily run initialization and submission
2. incident creation
3. incident status transition
4. dashboard aggregation

### Scope

- extract use-case services/actions for each workflow
- introduce transaction boundaries where appropriate
- keep Livewire components as UI orchestration endpoints only
- preserve behavior and test coverage
- incorporate authorization checks at the correct layer for extracted workflows where needed

### Deliverables

- application-layer classes for core workflows
- thinner Livewire components
- domain rules concentrated outside UI

### Acceptance criteria

- Livewire components no longer own core persistence orchestration
- business rule tests can run without UI-driven orchestration

### Risks

- regressions in happy-path behavior
- accidental over-abstraction

### Mitigation

- refactor one workflow at a time
- retain feature tests throughout
- add service-level tests as extraction proceeds

## Phase 5. Shell and Presentation Consolidation

### Goal

Remove duplicated shell logic and establish a clean, authoritative rendering structure.

### Scope

- consolidate app shell
- consolidate auth/settings shell strategy
- remove duplicated nav/user-menu structures
- decide the long-term fate of Volt-based settings pages after the shell is stable

### Deliverables

- one authoritative app shell
- reusable role-aware navigation definitions
- reduced duplicated markup

### Acceptance criteria

- app shell ownership is singular
- navigation changes require one logical change path
- settings/auth rendering is structurally consistent

### Risks

- layout regressions
- mobile/desktop navigation divergence during transition

### Mitigation

- regression tests for navigation and rendering
- visual/manual verification checklist

## Phase 6. Repository Hygiene and Source-of-Truth Cleanup

### Goal

Align version control contents with engineering intent.

### Scope

- review and remove generated/public artifacts that should not be tracked
- normalize project metadata
- remove starter residue and dead scaffolding
- review `⚡`-named files and decide:
  - keep intentionally as Volt convention if valid and documented
- or rename/migrate if they create tooling confusion
- review dead or misleading auth-related residue, including unsupported flows that remain in code but are not part of the intended product contract

### Deliverables

- explicit asset tracking policy
- cleaner repository
- updated metadata
- reduced non-source noise

### Acceptance criteria

- repository contents reflect source-of-truth policy
- generated artifacts are regenerated, not hand-curated
- starter identity leakage is removed

### Risks

- deployment/setup assumptions depending on currently tracked files

### Mitigation

- validate setup from clean clone
- document generation steps before removing tracked artifacts

## 7. Workstreams

Execution should be managed as seven coordinated workstreams.

## WS1. Platform and CI

Owns:

- PHP baseline
- Composer metadata and lock truth
- GitHub Actions
- setup contract

Dependencies:

- none

Must finish before:

- all deeper structural work

## WS2. Architecture and Boundaries

Owns:

- target layering
- ownership rules
- file placement conventions
- shell strategy decisions
- authorization boundary rules

Dependencies:

- WS1 platform truth

## WS3. Domain Model and Invariants

Owns:

- enums/value objects
- invariants
- shared business vocabulary

Dependencies:

- WS2 architectural direction

## WS4. Workflow Refactor

Owns:

- checklist use cases
- incident use cases
- dashboard query/application logic

Dependencies:

- WS2 and WS3

## WS5. Repository Hygiene

Owns:

- tracked artifacts policy
- metadata cleanup
- scaffold residue removal

Dependencies:

- WS1 for build/setup clarity
- WS2 for ownership clarity

## WS6. Environment and Operability

Owns:

- `.env.example` truth
- README/bootstrap parity
- local runtime contract
- storage link and attachment operability

Dependencies:

- WS1 platform truth

## WS7. Authorization and Account Lifecycle

Owns:

- active/inactive user enforcement
- verification policy
- registration policy
- access-control ownership beyond route middleware

Dependencies:

- WS2 architectural boundary definition
- WS3 domain normalization if role/account definitions are centralized

## 8. Sequencing Strategy

Recommended execution order:

1. Phase 0
2. Phase 1
3. Phase 2
4. Phase 3
5. Phase 4
6. Phase 5
7. Phase 6

Why this order is optimal:

- Phase 1 removes false platform assumptions.
- Phase 1 also removes environment/bootstrap contradictions and runtime operability surprises.
- Phase 2 prevents refactoring into another ambiguous state and locks security/access boundaries.
- Phase 3 creates canonical domain language before deep extraction and defines invariant ownership.
- Phase 4 moves logic into the right layer.
- Phase 5 consolidates presentation after the use cases are stable.
- Phase 6 cleans the repository once the build/source story is no longer ambiguous.

This ordering maximizes leverage and minimizes rework.

## 9. Execution Gates

No phase should be considered complete without meeting its gate.

## Gate G1. Platform Gate

Required before Phase 2 starts:

- CI green on supported baseline
- declared PHP support matches actual support
- setup contract documented
- local bootstrap contract verified from clean assumptions
- runtime storage/attachment prerequisites verified

## Gate G2. Architecture Gate

Required before Phase 3 starts:

- ownership map approved
- target layering documented
- shell authority decided
- authorization and account-lifecycle policy decided

## Gate G3. Domain Gate

Required before Phase 4 starts:

- canonical state definitions implemented or ready for immediate implementation
- no unresolved ambiguity around statuses/categories/results/roles
- data transition rules for domain-state normalization are defined

## Gate G4. Workflow Gate

Required before Phase 5 starts:

- one core workflow successfully extracted and validated
- extraction pattern proven repeatable

## Gate G5. Presentation Gate

Required before Phase 6 starts:

- authoritative shell established
- no critical navigation regressions

## Gate G6. Repository Gate

Required before remediation closeout:

- clean clone setup documented and verified
- tracked artifacts policy enforced

## 10. Testing Strategy for the Execute Phase

The planning choice is:

- keep high-value feature tests
- add application-layer tests as logic is extracted
- remove meaningless boilerplate tests when real coverage replaces them

### Test pyramid target

- Feature tests:
  - route access
  - workflow behavior
  - rendering-critical navigation
- Application/use-case tests:
  - checklist submission rules
  - incident creation rules
  - incident status transition rules
  - dashboard aggregation rules
- Domain tests:
  - enum/value mappings
  - transition rules
  - invariant enforcement

### Planning constraint

Do not reduce test confidence during refactor. Add lower-level tests before shrinking higher-level ones.

## 11. Risk Register

## R1. Refactor churn without stable target

Risk:

The team starts moving files before finalizing ownership rules.

Consequence:

Another layer of drift.

Response:

Do not execute structural changes before Phase 2 decisions are locked.

## R2. Over-engineering the application layer

Risk:

The codebase gets burdened with abstractions that exceed its size.

Response:

Use pragmatic application services and enums. Avoid introducing DDD ceremony without clear value.

## R3. Breaking working behavior while cleaning structure

Risk:

Because the app works today, careless refactoring can trade structural cleanliness for user-facing regressions.

Response:

- preserve feature tests
- introduce service-level tests
- refactor incrementally per workflow

## R4. Asset cleanup breaks setup or runtime

Risk:

Removing tracked public artifacts without validated regeneration steps breaks the app.

Response:

Validate from clean clone before final cleanup.

## R5. Mixed settings/auth strategy lingers too long

Risk:

Temporary coexistence of Volt and class-based Livewire becomes permanent by neglect.

Response:

Re-open that decision explicitly after Phase 5. Do not let "temporary" become undocumented permanent architecture.

## R6. Environment truth remains inconsistent after platform cleanup

Risk:

CI becomes green, but local developer experience remains inconsistent because `.env.example`, README, and runtime assumptions still disagree.

Response:

Treat environment/bootstrap parity as part of Phase 1 completion, not a later cleanup.

## R7. Authorization drift survives the refactor

Risk:

Workflow extraction happens, but access control remains split across route middleware, Filament gates, and implicit assumptions. Inactive users or unsupported account states remain operationally valid.

Response:

Define the authorization/account-lifecycle contract in Phase 2 before extracting use cases.

## 12. Non-Goals

The execution plan should explicitly avoid the following during the foundation remediation program:

- adding new business features
- redesigning the product scope
- introducing another framework to "solve" current inconsistency
- large rewrites without coverage
- premature microservice-style decomposition

## 13. Definition of Success

The remediation program is successful when all of the following are true:

1. CI is truthful and green.
2. The codebase has a documented and enforced architectural boundary model.
3. Core workflows execute through application-layer use cases, not UI-owned orchestration.
4. Business states are canonical and centrally defined.
5. The app shell is singular and maintainable.
6. The repo contains source of truth, not stale generated noise.
7. A new contributor can understand:
   - where code belongs
   - what the supported platform is
   - how the app is structured
   - how to run and verify it

## 14. Recommended Next Step

The correct next step is not implementation.

The correct next step is to convert this plan into an execution backlog with:

- task decomposition per phase
- dependency map
- estimated effort
- owner assignment
- review checkpoints
- rollback/verification notes

That should become the first Execute-phase input.

## 15. Final Decision Summary

For clarity, the plan chooses the following:

- PHP baseline: PHP 8.4 minimum
- UI ownership:
  - Filament for internal admin CRUD only
  - custom Livewire for core operations flows
  - Volt temporarily frozen to auth/settings only
- architecture style: pragmatic layered architecture, not ad hoc Livewire orchestration
- shell ownership: one authoritative app shell and one authoritative auth/settings shell strategy
- domain constants: centralized typed definitions
- asset policy: source-only repository by default
- sequencing: stabilize platform first, then boundaries, then domain, then workflow extraction, then shell consolidation, then repository cleanup

This is the most defensible path if the project wants long-term maintainability without wasting effort on ideological rewrites.
