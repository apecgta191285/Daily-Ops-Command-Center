# Architecture Debt Roadmap

Date: 2026-04-11
Project: Daily Ops Command Center
Purpose: Track debt that remains relevant after the foundation remediation program completed. This document is intentionally forward-looking. It should not duplicate already-closed execution logs.

Active refactor context:

- the repository is now operating under the master refactor program defined in `27_Full_Codebase_Audit_2026-04-11.md` and `28_Master_Refactor_Plan_2026-04-11.md`
- debt items below should be interpreted as workstreams to close through phased refactor, not as ad hoc cleanup suggestions

## 1. Closed or Materially Reduced Debt

### Platform truth debt

Status: materially reduced

Closed work:

- PHP support claim aligned to 8.4
- CI platform claim aligned to the supported baseline
- local bootstrap profile aligned to SQLite and documented in README

### Account lifecycle ambiguity debt

Status: materially reduced

Closed work:

- inactive-user enforcement exists at authentication and protected-route boundaries
- public registration ambiguity removed
- account policy documented explicitly in code and docs

### Workflow-orchestration-in-UI debt

Status: materially reduced

Closed work:

- checklist initialization extracted into application actions
- checklist submission extracted into application actions
- incident creation extracted into application actions
- incident transition extracted into application actions
- dashboard aggregation extracted into an application query

### Shell duplication debt

Status: materially reduced

Closed work:

- canonical operations shell selected
- duplicate header-based shell removed
- role-aware navigation centralized

### Admin surface fragmentation debt

Status: materially reduced

Closed work:

- checklist template administration consolidated into the main application shell
- separate admin panel/login surface retired from the active UX path
- legacy `/admin/*` checklist-template entry points retired from the supported route contract

### Repository source-of-truth debt

Status: materially reduced

Closed work:

- source-only artifact policy documented
- project metadata normalized away from starter-kit residue

## 2. Active Debt Still Remaining

### Presentation-literal drift debt

Status: open

Evidence:

- badge-color mappings and label transforms still exist in some Blade views
- some presentation components still depend directly on raw domain strings

Why it matters:

- visual consistency can drift
- domain representation rules can split between backend and Blade

Recommended handling:

- introduce shared presentation helpers or view models only when repeated duplication becomes material
- do not push display rules back into controllers or Livewire components without an explicit placement decision

### Frontend-contract modularity debt

Status: materially reduced

Evidence:

- app-owned tokens, shell styles, auth styles, and settings styles are now being separated into dedicated CSS modules
- settings/auth still use Flux primitives, but the contract is increasingly defined by project-owned classes rather than ad hoc overrides in one monolithic stylesheet

Why it matters:

- frontend drift accelerates when tokens, shell rules, form rules, and settings overrides all live in one file
- new contributors need a predictable placement model for frontend changes just as much as backend changes

Recommended handling:

- continue moving presentation rules toward app-owned modules
- defer large class-pattern migration until modular contract work stops yielding high-value improvements

### Flux-specific styling leakage debt

Status: materially reduced

Evidence:

- settings modals, recovery-code flows, and security interactions now lean on app-owned settings classes instead of one-off Flux defaults
- Flux remains present as a primitive UI/runtime layer, but a larger share of visual behavior is now defined through project-owned contract classes

Why it matters:

- the application feels fragmented when framework defaults keep owning the look of high-traffic internal surfaces
- app-owned classes give the team a maintainable place to evolve the design system without rewriting framework behavior

Recommended handling:

- continue shrinking framework-owned presentation decisions where repetition is high
- avoid chasing pixel-perfect replacement of every Flux primitive unless it produces real product value

### Domain-truth mismatch debt

Status: materially reduced

Evidence:

- checklist template `scope` now exists as canonical vocabulary, runtime dimension, and governance dimension
- daily checklist execution, dashboard signals, and template administration now share the same per-scope truth

Why it matters:

- the most dangerous mismatch from the earlier baseline has been removed
- future waves can build on real operational lanes instead of a fake single-flow assumption

Recommended handling:

- keep canonical docs aligned whenever runtime truth changes again
- avoid reintroducing singular-runtime language into new feature docs or UI copy

### Persistence-invariant coverage debt

Status: open but partially reduced

Evidence:

- high-risk template invariants are now being hardened selectively
- broader canonical string families still depend on domain/application enforcement

Why it matters:

- the repository is safer than before, but invalid business state is still not impossible in every table

Recommended handling:

- keep selective hardening for the most dangerous invariants now
- revisit wider DB constraint coverage only when the cost of schema rebuild is justified by platform direction

### Fixture-and-seed coupling debt

Status: materially reduced

Evidence:

- feature/application tests now have project-owned factories and scenario helpers for users, checklist templates, runs, incidents, and activities
- automated tests no longer need to depend on seeded demo titles, demo emails, or the full `DatabaseSeeder` narrative in most critical paths

Why it matters:

- demo narrative should be free to evolve without breaking unrelated regression tests
- predictable fixture construction lowers test brittleness and makes future refactors safer

Recommended handling:

- keep `DatabaseSeeder` focused on demo/bootstrap value
- prefer factory and scenario-helper state setup for all new behavior tests
- only use seeded narrative directly when the test is explicitly about bootstrap/demo behavior

### Documentation aging debt

Status: open

Evidence:

- canonical docs are now smaller and cleaner, but they still require deliberate upkeep when contracts change

Why it matters:

- stale docs will recreate the original truth-mismatch problem

Recommended handling:

- update only the canonical docs set when a contract changes
- avoid re-introducing temporary planning or execution trail artifacts into the permanent repo baseline

### Browser-level regression safety debt

Status: materially reduced

Evidence:

- the repository now has an active browser-smoke suite under `tests/Browser/**`
- CI workflow includes a dedicated browser job using Pest Browser + Playwright
- local execution on Linux/WSL still depends on host-level Playwright libraries being present

Why it matters:

- user-facing shell drift and broken browser flows are not caught reliably by server-only tests
- without an explicit browser harness, UI regressions reappear late and expensively

Recommended handling:

- keep the suite narrow and trusted
- treat CI as the authoritative browser execution surface until local host dependencies are standardized
- document local Playwright prerequisites instead of pretending the suite is zero-setup

### Scope-expansion debt

Status: open

Evidence:

- future requests may pressure the system toward assignment workflows, notifications, analytics, or broader command-center behavior

Why it matters:

- the codebase is healthier now, which can make uncontrolled scope expansion feel deceptively safe

Recommended handling:

- force new scope through Project Lock and Decision Log first
- treat large feature families as separate planning tracks instead of opportunistic extensions

## 3. Future Debt Prevention Rules

To avoid reintroducing the same debt classes:

- no new workflow orchestration should be added directly to Livewire components
- no new business-state family should be introduced without a canonical domain definition
- no new app shell variant should be introduced without an explicit architecture decision
- no generated artifact should be committed without a repository-policy justification
- no cross-phase scope expansion should happen without updating the active execution artifact

## 4. Future Decision Triggers

The following events require an explicit architectural decision rather than ad hoc implementation:

- adding a new operational workflow
- changing persisted business-state values
- introducing a second admin or internal tool surface
- changing authentication requirements for operational routes
- changing asset build/publish strategy

## 5. Recommended Ongoing Practice

At the end of each remediation phase:

- update the execution status document
- update this debt roadmap
- state which debt was closed, reduced, deferred, or newly discovered

This keeps remediation cumulative rather than anecdotal.
