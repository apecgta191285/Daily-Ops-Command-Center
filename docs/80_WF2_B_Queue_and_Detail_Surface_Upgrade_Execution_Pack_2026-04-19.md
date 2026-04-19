# WF2-B Queue and Detail Surface Upgrade Execution Pack

**Date:** 2026-04-19  
**Status:** Implemented  
**Parent Plan:** `77_WF2_Incident_Ownership_Lite_Master_Plan_2026-04-19.md`
**Execution Standard:** Accountability pressure must be visible, not inferred

---

## 1. Outcome

WF2-B is now live in the product.

The management incident queue can now surface accountability pressure directly through:

- unowned incidents
- incidents owned by the current management user
- overdue follow-up targets

The incident detail surface now also exposes accountability pressure explicitly instead of hiding it behind activity history alone.

---

## 2. What Landed

### Queue filter contract

Incident list now supports lightweight accountability filters:

- `unowned`
- `mine`
- `overdue`

These work alongside the existing:

- `unresolved`
- `stale`
- `status`
- `category`
- `severity`

### Shared accountability policy owner

`IncidentFollowUpPolicy` now owns the definition of:

- when an incident follow-up target is overdue
- how overdue follow-up is applied to unresolved incident queries

This keeps later dashboard work from duplicating overdue logic in multiple places.

### Incident queue surface

The incident list now shows:

- owner column
- follow-up target column
- richer attention column

So a management user can scan:

- who is carrying the incident
- whether a target exists
- whether the target is already overdue

### Incident detail surface

Incident detail now adds:

- visible `Needs owner` pressure
- visible `Follow-up overdue` pressure
- accountability callouts when a record is unresolved but ownership is missing or the target date has passed

---

## 3. Architectural Decisions

### Why a separate follow-up policy owner was added

Overdue semantics are now part of product truth, not presentation-only logic.

That means:

- queue
- detail
- future dashboard ownership pressure

all need to speak the same definition.

The wave therefore introduced `IncidentFollowUpPolicy` instead of repeating date/status checks in:

- Blade views
- query classes
- dashboard builders later

### What was intentionally not added

WF2-B still does **not** introduce:

- automatic reminders
- notification dispatch
- reassignment audit stream
- SLA timers
- escalation workflows

The queue is more accountable now, but still intentionally A-lite.

---

## 4. Verification

Passed after implementation:

- `composer lint:check`
- `php artisan test`
- `npm run build`
- `composer test:browser`

Coverage now proves:

- accountability query filters work
- overdue semantics ignore resolved incidents
- detail surface renders ownership pressure correctly
- browser smoke confirms the queue shows accountability filters and fields without regressions

---

## 5. Next Correct Step

The next valuable slice is:

`WF2-C Dashboard Ownership Pressure`

Now that queue and detail surfaces can express accountability honestly, the dashboard can summarize:

- unowned unresolved incidents
- overdue follow-up pressure
- ownership gaps that need management attention

without inventing fake reporting logic or adding analytics infrastructure.
