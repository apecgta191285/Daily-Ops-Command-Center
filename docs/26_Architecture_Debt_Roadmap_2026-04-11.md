# Architecture Debt Roadmap

Date: 2026-04-11
Project: Daily Ops Command Center
Purpose: Track debt that remains relevant after the foundation remediation program completed. This document is intentionally forward-looking. It should not duplicate already-closed execution logs.

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

### Repository source-of-truth debt

Status: materially reduced

Closed work:

- source-only artifact policy documented
- vendor-generated Filament assets removed from tracked history going forward
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

### Documentation aging debt

Status: open

Evidence:

- canonical docs are now smaller and cleaner, but they still require deliberate upkeep when contracts change

Why it matters:

- stale docs will recreate the original truth-mismatch problem

Recommended handling:

- update only the canonical docs set when a contract changes
- avoid re-introducing temporary planning or execution trail artifacts into the permanent repo baseline

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
