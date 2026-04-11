# Full Codebase Audit

Date: 2026-04-11
Project: Daily Ops Command Center
Scope: full repository audit against the current working tree baseline, including uncommitted consolidation work already present locally on 2026-04-11
Purpose: identify structural defects, root causes, and refactor targets before any large-scale system-wide remediation plan
Status: audit-only document; this is not a refactor plan and should not be treated as a canonical product contract by itself

## 1. Executive Verdict

The repository is no longer in the "broken MVP with no baseline" state. The foundation is materially better than before:

- runtime baseline is coherent enough to boot, test, and iterate
- core use cases have application-layer extraction in the most important workflows
- the separate Filament admin surface is being retired in favor of a single application shell
- lint and automated tests are green on the current working tree

But the repository is still not production-grade in the strict sense the project now wants.

The most important truth is this:

- the project does not primarily suffer from one catastrophic bug
- it suffers from several unresolved contract mismatches across domain, persistence, presentation, documentation, and regression strategy

This means a large refactor can be justified, but only if it is driven by explicit contracts and phased remediation. A full rewrite or broad "clean up everything" push would be wasteful and dangerous.

## 2. What The System Actually Is Right Now

Current product shape in code:

- staff run one daily checklist flow and can report incidents
- supervisors review incidents and the dashboard
- admins do the same as supervisors and can manage checklist templates

Current implementation shape:

- operations and admin presentation now largely live in the main Livewire app shell
- auth and settings still live in a separate Flux or Volt-style page pattern
- core workflow orchestration exists in `app/Application/**`
- persistence still relies directly on Eloquent models without repository abstraction
- business state still persists mostly as unconstrained strings in the database

This means the repository is now a pragmatic modular monolith with:

- partially normalized domain contracts
- partially unified presentation contracts
- partially unified docs and persistence contracts

That "partially" is where most remaining debt comes from.

## 3. Audit Method

This audit reviewed:

- canonical docs in `docs/00` through `docs/06`, `docs/22`, `docs/24`, and `docs/26`
- runtime and dependency files such as `composer.json`, `.env.example`, `README.md`, and GitHub workflows
- routes, middleware, providers, Livewire components, Blade views, application actions, models, migrations, seeders, and tests
- current route surface via `php artisan route:list --except-vendor`
- current repository quality signal via green lint and green automated tests on the working tree

## 4. Highest-Severity Findings

### F-01. Checklist template domain model is more complex than the runtime actually supports

Severity: critical

Evidence:

- `app/Domain/Checklists/Enums/ChecklistScope.php`
- `app/Application/Checklists/Actions/InitializeDailyRun.php`
- `app/Application/ChecklistTemplates/Actions/SaveChecklistTemplate.php`
- `database/migrations/2026_04_05_000002_create_checklist_templates_table.php`
- `resources/views/livewire/admin/checklist-templates/manage.blade.php`

Observed condition:

- the data model and admin UI expose checklist template `scope`
- the system also supports multiple template records
- but the daily checklist runtime still resolves one globally active template only
- saving one template as active retires every other template across all scopes

Why this matters:

- the codebase models a scoped-template system
- the runtime executes a singular-template system
- the UI currently suggests more flexibility than the business workflow actually honors

Practical consequence:

- `scope` is currently descriptive metadata more than a true execution discriminator
- future developers can easily overestimate what the system supports
- this is a domain contract mismatch, not just a UI issue

Root cause:

- the project introduced scope vocabulary early
- but the operational workflow stayed locked to one active checklist path for MVP simplicity
- later normalization work did not yet collapse or formalize that contradiction

Audit verdict:

- the project must choose one truth
- either keep singular daily execution and simplify the domain around it
- or evolve execution to be truly scope-aware

Do not leave the current halfway state in place during a large refactor.

### F-02. Critical business invariants are enforced in application code but not at the database level

Severity: critical

Evidence:

- `database/migrations/2026_04_05_000002_create_checklist_templates_table.php`
- `database/migrations/2026_04_05_000006_create_incidents_table.php`
- `database/migrations/2026_04_05_000001_add_role_and_is_active_to_users_table.php`
- `database/migrations/2026_04_05_000005_create_checklist_run_items_table.php`
- `app/Application/ChecklistTemplates/Actions/SaveChecklistTemplate.php`

Observed condition:

- `role`, `scope`, `status`, `severity`, `category`, and `result` are persisted as plain strings
- there are no database check constraints protecting canonical values
- there is no database-level protection for the "only one active template" rule
- the strongest template invariant currently lives only inside the save action

Why this matters:

- any future code path, seed, tinker command, import job, or hotfix can silently create invalid state
- concurrency can violate business assumptions if two templates are activated in competing transactions
- the repository appears safer than it really is because the current UI path is clean

Root cause:

- the original MVP optimized for speed and simple migrations
- later domain normalization introduced enums in PHP without completing persistence-level enforcement

Audit verdict:

- for a production-grade baseline, this is one of the most important remaining gaps
- a high-standard refactor must include persistence contract hardening

### F-03. Canonical and historical truth are still mixed together in developer-facing artifacts

Severity: high

Evidence:

- `.env.example`
- `README.md`
- `docs/05_Decision_Log_v1.3.md`
- `docs/04_Current_State_v1.3.md`

Observed condition:

- `.env.example` still says `APP_NAME=Laravel`
- `README.md` correctly describes Daily Ops Command Center
- `docs/05_Decision_Log_v1.3.md` still contains earlier locked decisions that reference Filament 5, MySQL local, and older stack assumptions as part of the append-only history
- newer decisions supersede them, but the file still demands interpretation rather than giving a clean operational truth

Why this matters:

- a senior-grade team can live with append-only history only if current truth is impossible to misread
- here, current truth is understandable, but not cleanly separated enough from historical truth

Root cause:

- the project preserved decision history but did not sufficiently partition "historical context" from "active engineering contract"

Audit verdict:

- docs are improved, but not yet fully production-grade as a living system-of-record
- this is a maintainability issue, not a cosmetic issue

## 5. High-Severity Findings

### F-04. Domain enum adoption is incomplete and raw string drift is still widespread

Severity: high

Evidence:

- `app/Domain/Access/Enums/UserRole.php`
- `app/Domain/Incidents/Enums/IncidentStatus.php`
- `app/Domain/Checklists/Enums/ChecklistResult.php`
- `app/Models/User.php`
- `app/Http/Middleware/EnsureUserHasRole.php`
- `app/Livewire/Management/Incidents/Show.php`
- `database/seeders/DatabaseSeeder.php`
- many tests under `tests/Feature/**`

Observed condition:

- enums exist for role, status, result, and scope
- but string literals are still used directly in models, middleware, validation, seeders, tests, and parts of the view layer

Why this matters:

- the repository has already paid the cost of introducing canonical enums
- but it has not yet captured the maintainability benefit
- future state drift can still happen through raw-string duplication

Root cause:

- normalization began in the application layer first
- follow-through across persistence, middleware, tests, and fixtures remains incomplete

Audit verdict:

- this is one of the clearest refactor candidates because it has high leverage and low conceptual ambiguity

### F-05. Presentation ownership is still split across different UI authoring models

Severity: high

Evidence:

- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/auth/simple.blade.php`
- `resources/views/pages/auth/*.blade.php`
- `resources/views/pages/settings/⚡*.blade.php`
- `resources/css/app.css`

Observed condition:

- operational and admin pages now live in the main app shell
- auth pages use the shared design tokens but are still authored separately
- settings pages are inline anonymous Livewire or Volt-style pages inside Blade files
- the CSS file is a growing monolith that owns shell, cards, forms, auth, settings, sidebar, menu, and presentation tokens together

Why this matters:

- the visual system is more unified than before, but the authoring model is still fragmented
- this makes future frontend refactoring harder because behavior ownership and view ownership are not uniform

Root cause:

- prior remediation focused on getting one visual direction
- it did not yet complete the deeper consolidation of page composition patterns

Audit verdict:

- the app is not "many random websites" anymore
- but the frontend is still not a fully coherent platform in engineering terms

### F-06. Settings pages are structurally awkward and hard to govern

Severity: high

Evidence:

- `routes/settings.php`
- `resources/views/pages/settings/⚡profile.blade.php`
- `resources/views/pages/settings/⚡security.blade.php`
- `resources/views/pages/settings/⚡appearance.blade.php`
- `resources/views/pages/settings/⚡two-factor-setup-modal.blade.php`
- `resources/views/pages/settings/two-factor/⚡recovery-codes.blade.php`

Observed condition:

- settings pages are routed through `pages::settings.*`
- business behavior lives in anonymous component classes embedded inside Blade files
- filenames contain discovery-style `⚡` names
- related modal and recovery-code behavior also lives in Blade-first files

Why this matters:

- this pattern works, but it is weaker on navigability, static discoverability, and refactor ergonomics than named classes under `app/Livewire/**`
- it also creates a different code-reading experience from the rest of the product

Root cause:

- settings/auth retained the Flux or Volt authoring mode while operations/admin moved to explicit Livewire classes

Audit verdict:

- this is not an urgent bug
- but it is a strong maintainability smell and a likely future consolidation target

### F-07. Legacy `/admin/*` compatibility routes keep an obsolete mental model alive

Severity: high

Evidence:

- `routes/web.php`
- `tests/Feature/AdminSurfaceBoundaryTest.php`
- `php artisan route:list --except-vendor`

Observed condition:

- the active admin panel is retired
- but `/admin`, `/admin/checklist-templates`, and related edit/create URLs still exist as redirects

Why this matters:

- this preserves backward compatibility, which is sometimes useful
- but it also preserves the old conceptual split that the refactor is trying to eliminate

Root cause:

- the migration away from Filament optimized for safe transition and reversible rollout

Audit verdict:

- these routes should not survive indefinitely
- they are transition scaffolding, not a long-term contract

### F-08. The project has strong server-side tests but weak front-end confidence for the exact surface that has caused the most pain

Severity: high

Evidence:

- current test suite under `tests/Feature/**`
- no browser automation harness in active use
- `docs/05_Decision_Log_v1.3.md` still mentions Dusk historically, but there is no active browser-regression layer

Observed condition:

- route, access, action, and persistence behavior are tested well
- browser rendering, interactive appearance, responsive integrity, and shell coherence are not tested automatically

Why this matters:

- the project's most painful issues have been UX consistency, layout drift, and surface confusion
- those are exactly the areas least protected by the current test strategy

Root cause:

- the test program is backend-heavy because it was optimized for foundation correctness first

Audit verdict:

- a high-standard refactor should include at least one browser-level regression path for the primary user journeys

## 6. Medium-Severity Findings

### F-09. Seed data is acting as both demo fixture and implicit test contract

Severity: medium

Evidence:

- `database/seeders/DatabaseSeeder.php`
- multiple feature tests that query for named seeded records

Observed condition:

- tests depend on seeded roles, templates, incidents, and specific titles
- the seeder uses `firstOrCreate`, which preserves existing rows and does not reconcile changed values

Why this matters:

- this makes the demo dataset stable enough to use repeatedly
- but it also allows silent fixture drift over time
- a changed local database can diverge from the intended baseline while tests continue to pass in some scenarios

Root cause:

- the project uses one seeded dataset as both demo narrative and test scaffolding

Audit verdict:

- this is practical for MVP work
- but production-grade maintainability wants cleaner separation between demo seeds and deterministic test fixtures

### F-10. Localization and product copy strategy is not standardized

Severity: medium

Evidence:

- Thai checklist template titles and incident titles in `DatabaseSeeder.php`
- English route/page labels in many Blade files
- Thai and English mixed throughout docs and UI

Observed condition:

- the project is bilingual in an ad hoc way
- it does not appear to have a clear product-language strategy

Why this matters:

- mixed-language UX can be acceptable during prototyping
- but it becomes a product-quality and maintainability issue once copy starts spreading

Audit verdict:

- decide whether the product baseline is Thai-first, English-first, or bilingual-by-design
- then encode that decision in copy and localization conventions

### F-11. Front-end behavior ownership is still underdefined

Severity: medium

Evidence:

- `resources/js/app.js` is empty
- `resources/views/partials/head.blade.php` relies on `@fluxAppearance`
- appearance behavior depends on library-owned behavior rather than app-owned behavior

Observed condition:

- the app now has strong CSS tokens
- but almost no explicit app-owned frontend behavior layer

Why this matters:

- the frontend currently works largely because server rendering and Flux defaults cooperate
- not because the product has an explicit UI behavior contract of its own

Audit verdict:

- this is fine for the current size
- but if settings, interactive admin forms, or richer workflow UX expand, the lack of owned UI behavior infrastructure will matter

### F-12. Validation and business policy are still split between Livewire and application actions

Severity: medium

Evidence:

- `app/Livewire/Admin/ChecklistTemplates/Manage.php`
- `app/Application/ChecklistTemplates/Actions/SaveChecklistTemplate.php`
- `app/Livewire/Staff/Incidents/Create.php`
- `app/Application/Incidents/Actions/CreateIncident.php`

Observed condition:

- Livewire components validate field shape
- actions validate deeper domain admissibility
- the split is reasonable, but not formalized consistently

Why this matters:

- without an explicit validation policy, future code can duplicate or contradict rules across layers

Audit verdict:

- this is not yet broken
- but it should be formalized during a large refactor

## 7. Lower-Severity But Important Observations

### F-13. `.env.example` still carries starter residue

Severity: low

Evidence:

- `.env.example` sets `APP_NAME=Laravel`

Why it matters:

- this is small, but it is exactly the kind of detail that signals incomplete standardization

### F-14. The CSS contract is getting large enough to deserve structure

Severity: low

Evidence:

- `resources/css/app.css`

Observed condition:

- design tokens, shell styles, controls, auth, settings, menus, tables, and helper styles all live in one file

Why it matters:

- this is still manageable today
- it will become a friction point during broad frontend refactors

### F-15. Role-based authorization is route-centric and middleware-centric, not policy-centric

Severity: low

Evidence:

- `app/Http/Middleware/EnsureUserHasRole.php`
- route groups in `routes/web.php`

Observed condition:

- route-level role middleware is doing most authorization work
- there is no policy layer for more granular action decisions

Why it matters:

- this is acceptable for the current feature set
- but it will scale poorly if object-level permissions or more nuanced admin behavior appear

## 8. Root-Cause Clusters

### Cluster A. Product concept outran runtime simplification

Symptoms:

- scoped templates exist
- singular daily run flow still governs execution
- admin CRUD exposes concepts that runtime does not fully honor

True cause:

- the system kept richer vocabulary than the workflow currently supports

### Cluster B. Normalization started in PHP before it reached persistence and fixtures

Symptoms:

- enums exist
- strings still dominate database fields, seeders, tests, and middleware

True cause:

- the project normalized code semantics without yet completing the persistence contract

### Cluster C. Presentation became visually closer before it became structurally uniform

Symptoms:

- shared shell improved
- auth/settings still use a different authoring pattern
- CSS ownership is centralized visually but not structurally

True cause:

- the refactor optimized for immediate UX coherence rather than complete frontend architecture consolidation

### Cluster D. Documentation became smaller, but not yet surgically separated into active truth vs historical record

Symptoms:

- canonical docs are cleaner
- historical decisions still require interpretation
- bootstrap files still contain starter residue

True cause:

- cleanup removed noise faster than it formalized doc governance rules

## 9. What This Means For A Large Refactor

The repository is ready for a serious refactor plan, but not for an uncontrolled rewrite.

The correct interpretation is:

- backend foundation is good enough to preserve
- application-layer extraction should be extended, not discarded
- presentation and persistence contracts need the next major round of discipline
- docs and test strategy need strengthening to prevent drift from reappearing

The project should not:

- reintroduce a separate admin surface
- perform a broad rewrite without explicit domain and persistence decisions
- treat frontend cleanup as a purely visual exercise

The project should:

- decide whether checklist scope is real workflow state or just metadata
- harden database invariants for role, status, result, scope, and active-template rules
- finish enum adoption end-to-end
- formally choose the future of settings/auth authoring
- add at least one browser-level regression path

## 10. Refactor Readiness Verdict

The repository is ready for:

- a full refactor planning phase
- explicit workstream decomposition
- architecture-level cleanup guided by contracts

The repository is not ready for:

- a "clean everything everywhere" rewrite
- a cosmetic-only frontend pass that ignores persistence and domain mismatches
- a production-grade claim without further hardening

## 11. Final Brutal Truth

The project now has a workable foundation, but not yet a fully trustworthy one.

The biggest remaining problems are no longer "things are random."
They are more dangerous than that:

- the system now looks coherent enough that people can overestimate how finished its contracts really are

That is exactly the point where disciplined refactoring matters most.

This audit concludes that the next step should be a formal refactor plan organized around:

- domain contract cleanup
- persistence invariant hardening
- frontend architecture consolidation
- documentation truth partitioning
- browser-level regression safety
