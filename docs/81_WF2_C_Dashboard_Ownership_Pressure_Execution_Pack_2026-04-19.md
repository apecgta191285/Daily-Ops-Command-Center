# WF2-C Dashboard Ownership Pressure Execution Pack

**Date:** 2026-04-19  
**Status:** Implemented  
**Parent Plan:** `77_WF2_Incident_Ownership_Lite_Master_Plan_2026-04-19.md`
**Execution Standard:** Compact action signals, not analytics bloat

---

## 1. Outcome

WF2-C is now live in the dashboard.

The management dashboard no longer summarizes incident pressure only through:

- severity
- stale age
- intake volume

It now also surfaces ownership pressure through:

- unowned unresolved incidents
- overdue follow-up targets
- incidents currently owned by the signed-in management user

---

## 2. What Landed

### Actor-aware dashboard snapshot

`GetDashboardSnapshot` now accepts an optional actor ID so the dashboard can summarize:

- incidents owned by the current management user

without leaking authentication concerns into application support classes or Blade.

### Ownership pressure owner

`DashboardOwnershipPressureBuilder` now owns compact dashboard ownership summary shaping.

This keeps ownership pressure separate from:

- queue filtering logic
- stale policy
- dashboard trend/hotspot shaping

### Attention lane upgrade

Dashboard attention can now add high-priority cards for:

- unowned unresolved incidents
- overdue follow-up targets

with direct drill-down routes into the incident queue.

### Compact accountability card

Dashboard now includes a compact `Accountability Signals` section that summarizes:

- incidents you own
- unowned unresolved incidents
- overdue follow-up incidents

while preserving the dashboard as a command surface instead of turning it into a full queue replica.

---

## 3. Architectural Decisions

### Why actor ID was passed in explicitly

The dashboard query now needs one user-relative signal:

- `ownedByActorCount`

Instead of reading auth state inside support classes, the wave passes actor ID from the controller into the query owner explicitly.

This keeps:

- controller = request/auth boundary
- query = application assembly owner

cleanly separated.

### Why a separate dashboard ownership builder exists

Ownership pressure is a new summary concern, but it is not the same thing as:

- dashboard attention assembly
- queue query filtering
- overdue follow-up policy

That is why the wave introduced `DashboardOwnershipPressureBuilder` rather than pushing more responsibility into an existing builder.

---

## 4. Verification

Passed after implementation:

- `composer lint:check`
- `php artisan test`
- `npm run build`
- `composer test:browser`

Coverage now proves:

- dashboard snapshot exposes ownership pressure summary
- dashboard page renders ownership pressure signals and drill-down actions
- browser smoke confirms `Accountability Signals` appears without regressions

---

## 5. Next Correct Step

The next valuable slice is:

`WF2-D Quality Hardening and Documentation`

That round should close WF2 by:

- aligning canonical docs with incident ownership truth
- reviewing any remaining route/query copy for accountability language
- confirming that no hidden ownership logic still lives only in UI wording
