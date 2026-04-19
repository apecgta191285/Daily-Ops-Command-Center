# WF2-A Ownership and Follow-Up Core Execution Pack

**Date:** 2026-04-19  
**Status:** Planning locked, ready for implementation  
**Parent Plan:** `77_WF2_Incident_Ownership_Lite_Master_Plan_2026-04-19.md`
**Execution Standard:** No Quick & Dirty, no hidden ownership, no pseudo-enterprise workflow

---

## 1. Objective

Land the minimum persistence and application truth for incident ownership.

This pack should establish:

- optional management owner
- optional follow-up target date

without yet trying to solve the whole queue experience in one jump.

---

## 2. Why This Must Be First

WF2 cannot start from UI polish.

If we add:

- “Owned by me”
- “Unowned”
- “Overdue”

filters before owner and follow-up truth exist in the model, we create fake product language.

WF2-A exists so later queue/detail/dashboard work can speak honestly.

---

## 3. Scope

### In scope

- add nullable `owner_id` to incidents
- add nullable `follow_up_due_at` to incidents
- restrict owners to management-capable users at application validation level
- extend incident transition/update owner so ownership and follow-up target can be changed safely
- update tests for persistence and action behavior

### Out of scope

- queue filters
- dashboard ownership pressure
- reassignment history
- notifications
- automatic follow-up policies

---

## 4. Data Contract

### Incident additions

New fields:

- `owner_id` nullable foreign key to `users.id`
- `follow_up_due_at` nullable datetime/date field

### Rules

1. `owner_id` may be null.
2. `owner_id` must reference a management-capable user.
3. `follow_up_due_at` may be null.
4. `follow_up_due_at` is an operational target date, not an SLA.
5. Status transition flow must remain backward-compatible if owner/follow-up fields are absent from the request.

---

## 5. Likely Touch Points

- `database/migrations/**`
- `app/Models/Incident.php`
- `app/Application/Incidents/Actions/TransitionIncidentStatus.php`
- `app/Livewire/Management/Incidents/Show.php`
- incident-related feature tests
- browser smoke where incident detail is exercised

Depending on final implementation shape, a new application action may be justified if ownership changes should not be overloaded into the existing status-transition owner.

---

## 6. Architectural Guidance

### Preferred direction

Keep incident status transition as the owner of:

- status changes
- resolution timestamp handling
- activity creation

But avoid turning it into a bloated “everything incident-related” action.

### Acceptable implementation paths

#### Path A: Extend existing transition action carefully

Use the current action if:

- owner/follow-up changes remain tightly coupled to the same update lane
- validation stays readable
- tests remain explicit

#### Path B: Introduce a separate lightweight incident accountability action

Prefer this if:

- ownership/follow-up changes need clearer separation from pure status transitions
- the existing transition action starts becoming multi-purpose and harder to reason about

### Recommendation

Start by designing for **Path A**, but switch to **Path B** immediately if readability drops.

The rule is:

> do not protect “small file count” at the cost of losing application ownership clarity.

---

## 7. Test Plan

At minimum:

- feature test: incident owner can be set to admin/supervisor
- feature test: staff cannot become incident owner
- feature test: follow-up target can be stored and cleared
- feature test: status transition still updates resolved timestamp correctly
- feature test: status-only update still works when ownership fields are untouched
- browser smoke: incident detail still loads without JS/layout regressions

---

## 8. Success Criteria

WF2-A is done only when:

1. incidents persist optional owner and optional follow-up target safely
2. invalid owner assignment is rejected
3. current incident status workflow still passes regression tests
4. the repository has one clear ownership truth for accountability fields
5. later WF2-B queue work can build on real state rather than inferred activity text

---

## 9. Recommended Next Step After This Pack

Implement **WF2-A** in one controlled code round.

Do not skip straight to queue filters or dashboard pressure first.
