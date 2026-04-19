# WF2 Incident Ownership Lite Master Plan

**Date:** 2026-04-19  
**Status:** Complete wave baseline  
**Execution Standard:** No Quick & Dirty, no enterprise assignment matrix, no passive-record drift

---

## 1. Why WF2 Exists

The current incident flow already supports:

- incident reporting
- management review
- status transitions
- follow-up note quality
- resolution summary capture

But it still stops short of minimal accountability.

Right now the product can answer:

- what was reported
- how serious it is
- what status it is in

But it still cannot answer clearly:

- who owns the next move
- when the incident should be reviewed again

That is why incidents can still feel like passive records instead of active tracked work.

WF2 exists to fix that with a deliberately small accountability model.

---

## 2. WF2 Product Goal

Add enough ownership structure to make incidents feel like real tracked work for a small team.

After WF2:

- unresolved incidents can optionally have a management owner
- unresolved incidents can optionally carry a follow-up target date
- management can filter the queue by accountability pressure
- dashboard can reflect ownership pressure without becoming an analytics product

---

## 3. What WF2 Is Not

WF2 will **not** introduce:

- reassignment history engine
- SLA math
- escalation chains
- notifications
- approval workflow
- enterprise team matrix
- comments/chat system

The target is:

> one lightweight owner, one lightweight follow-up target, one clearer queue

---

## 4. Target Ownership Model

### Current model

- incidents have creator
- incidents have status
- incidents may have follow-up or resolution notes
- unresolved work has no explicit owner

### Target model

- each unresolved incident may have `owner_id`
- each unresolved incident may have `follow_up_due_at`
- owner is optional, not mandatory
- follow-up target is optional, not mandatory
- resolved incidents may clear ownership pressure semantics

### Product rules

1. Ownership is intentionally lightweight and management-only.
2. Owner selection is restricted to management-capable users.
3. Follow-up date is a simple target date, not an SLA promise.
4. Queue filters must support at least:
   - unowned
   - mine
   - overdue follow-up
5. Dashboard signals must remain operational and compact.

---

## 5. Phase Map

### WF2-A Ownership and Follow-Up Core

**Goal**

Make incident ownership real in persistence and action flow.

**Scope**

- add `owner_id` to incidents
- add `follow_up_due_at` to incidents
- update transition logic to support ownership/follow-up changes safely
- update tests around persistence and transition behavior

**Success criteria**

- incidents can store optional owner and follow-up date
- ownership changes do not break current status flow
- invalid owner selection is rejected safely

---

### WF2-B Queue and Detail Surface Upgrade

**Goal**

Make the incident list and detail surfaces visibly accountable.

**Scope**

- add queue filters for unowned / mine / overdue
- show owner and follow-up status in list/detail
- extend action lane on incident detail to manage ownership and follow-up target
- keep UI light and scan-friendly

**Success criteria**

- management can find unowned incidents quickly
- management can find their own incidents quickly
- overdue follow-up is visible without extra analytics tooling

---

### WF2-C Dashboard Ownership Pressure

**Goal**

Reflect accountability pressure on the dashboard without bloating it.

**Scope**

- add unresolved ownership pressure summary
- add counts for unowned and overdue incidents
- keep dashboard signal language compact and action-oriented

**Success criteria**

- dashboard helps management spot accountability gaps
- no chart/report builder complexity is introduced

---

### WF2-D Quality Hardening and Documentation

**Goal**

Close the wave with regression proof and canonical docs alignment.

**Scope**

- feature/unit/browser coverage
- README and current state update
- data definition / decision log updates if contract changes

**Success criteria**

- ownership truth is fully documented
- tests prove the lightweight accountability model end to end

**Completion note**

- canonical docs now describe incident accountability as first-class truth
- README/current-state summaries now treat dashboard, queue, and detail ownership pressure as one product language
- WF2 closes without introducing notifications, SLA engines, reassignment history, or enterprise workload routing

---

## 6. Recommended Execution Order

1. **WF2-A Ownership and Follow-Up Core**
2. **WF2-B Queue and Detail Surface Upgrade**
3. **WF2-C Dashboard Ownership Pressure**
4. **WF2-D Quality Hardening and Documentation**

### Why this order

- persistence truth must exist before UI can manage it
- queue/detail surfaces must reflect real fields before dashboard summarizes them
- dashboard should summarize accountability after the queue becomes real

---

## 7. Engineering Principles for WF2

1. Do not introduce assignment complexity that the team cannot operate manually.
2. Do not hide ownership truth inside incident activities only; it needs first-class state.
3. Keep owner semantics lightweight:
   - one owner
   - optional
   - management-only
4. Keep follow-up target semantics lightweight:
   - one date
   - optional
   - operational reminder, not SLA engine
5. Preserve current incident activity trail and status semantics.

---

## 8. Key Risks

### Risk 1: Overbuilding ownership

If WF2 adds reassignment workflows, escalation, and notifications too early, the product will leave A-lite scope.

### Risk 2: Hidden accountability

If `owner_id` exists only in schema but is not visible in queue/detail/dashboard, the feature will land as invisible debt.

### Risk 3: Status/ownership confusion

If resolved incidents still read like active accountability items without clear semantics, the queue will become noisy.

---

## 9. Expected Product Effect

After WF2 completes, the product should feel like:

- checklist work is structured by scope
- incidents are carried by someone, not just recorded
- management can see both operational pressure and accountability pressure

That is the smallest meaningful step from:

- “we can report and update incidents”

to:

- “we can track who is carrying unresolved work next”

without turning the app into enterprise operations software.
