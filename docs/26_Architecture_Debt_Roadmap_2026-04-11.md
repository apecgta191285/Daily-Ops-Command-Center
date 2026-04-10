# Architecture Debt Roadmap

Date: 2026-04-11
Project: Daily Ops Command Center
Purpose: Track architectural debt that has been identified, partially remediated, or intentionally deferred.

## 1. Closed or Materially Reduced Debt

### Platform truth debt

Status: materially reduced

Closed work:

- PHP support claim aligned to 8.4
- CI platform claim aligned
- local bootstrap profile aligned to SQLite

### Account lifecycle ambiguity debt

Status: materially reduced

Closed work:

- inactive-user enforcement exists
- registration ambiguity removed
- account policy documented explicitly

### Workflow-orchestration-in-UI debt

Status: materially reduced

Closed work:

- checklist initialization extracted
- checklist submission extracted
- incident creation extracted
- incident transition extracted
- dashboard aggregation extracted

Remaining residue:

- presentation components still hold some display-oriented literal mappings

## 2. Active Debt Still Remaining

### Shell duplication debt

Status: open

Evidence:

- app shell navigation is still duplicated across header/sidebar structures
- auth/settings shell family remains separate from the operations shell family

Planned phase:

- Phase 5

### Presentation-literal drift debt

Status: open

Evidence:

- badge-color mappings remain in Blade views
- some UI components still reference raw domain strings directly

Planned phase:

- Phase 5 and Phase 6

### Repository source-of-truth debt

Status: open

Evidence:

- generated/public asset policy still needs final cleanup
- repository still contains source-vs-generated ambiguity from earlier imports

Planned phase:

- Phase 6

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
