# WF1 Scoped Daily Operations Runtime Master Plan

**Date:** 2026-04-18  
**Status:** Approved planning baseline  
**Execution Standard:** No Quick & Dirty, no trial-and-error feature drift, no enterprise overreach

---

## 1. Why WF1 Exists

The current product already has:

- checklist templates
- checklist runs
- role boundaries
- admin template governance
- staff daily execution

But the runtime model still only supports:

> one active checklist template across the whole system

That means `ChecklistScope` is still mostly administrative metadata, not a real operating dimension.

This is the strongest reason the product still feels smaller than it looks.

WF1 exists to fix that by turning the app from:

- one daily checklist lane

into:

- a small daily operations system with real recurring work moments

without turning the codebase into a multi-tenant workflow platform.

---

## 2. WF1 Product Goal

### Core outcome

Support one active checklist template **per scope**, where scope is a real operational moment:

- opening
- midday
- closing

### Product effect

After WF1:

- staff will see a real daily operations board instead of one generic checklist lane
- admin will govern live templates per operational moment
- dashboard can talk about work by scope, not only aggregate totals
- the system will feel like it supports a real day, not just one checklist form

---

## 3. What WF1 Is Not

WF1 will **not** introduce:

- checklist draft workflow
- approval flow
- scheduler/cron orchestration
- recurring calendar engine
- assignment model
- multi-team / multi-branch execution
- complex availability rules

This is still A-lite.

The target is:

> one active template per scope, one run per scope per day per staff, one simple runtime board

---

## 4. Target Runtime Model

### Current model

- single active template globally
- `/checklists/runs/today` opens one run
- `scope` is classification metadata only

### Target model

- one active template per scope
- `/checklists/runs/today` becomes a scope-aware staff workboard
- each scope can open its own run
- each run remains simple and follows existing submit semantics

### Runtime rules

1. Active template uniqueness is enforced **per scope**
2. A staff user can have one run per `(scope, date, user)` through the active template for that scope
3. Each scope remains independent in execution and submission
4. The app does not auto-generate all scope runs eagerly; it should create on entry or launch of each scope lane
5. If a scope has no active template, the UI must show a calm but clear empty/configuration state

---

## 5. Phase Map

### WF1-A Domain and Persistence Realignment

**Goal**

Change the underlying template activation and run uniqueness model so scope becomes operationally real.

**Scope**

- change active-template invariant from global-single-active to per-scope-single-active
- update persistence constraints and supporting tests
- update activation messaging so admin understands scope-local activation
- preserve current semantics for historical runs

**Key areas**

- checklist template persistence invariant
- save/duplicate actions
- data definition / docs
- tests for scope-local active uniqueness

**Success criteria**

- system allows one active template in `OPENING` and one active template in `CLOSING` simultaneously
- system still forbids two active templates in the same scope
- admin activation messaging clearly explains scope-local replacement

---

### WF1-B Staff Runtime Board and Scope Entry

**Goal**

Replace the single checklist runtime entry with a real “today's operations” board for staff.

**Scope**

- turn `/checklists/runs/today` into a board that shows available scopes
- allow staff to open each scope lane explicitly
- create runs lazily for the chosen scope if missing
- keep submit flow per run simple and familiar

**User effect**

- staff understands the day as phases
- the system feels more like a real operations console

**Success criteria**

- staff can clearly distinguish opening, midday, and closing work
- scope lane states show at least: not started / in progress / submitted / unavailable
- existing checklist execution quality is preserved

---

### WF1-C Scope-Aware Dashboard and Signals

**Goal**

Expose scope awareness to management without adding analytics bloat.

**Scope**

- show checklist completion by scope
- highlight missing or incomplete scope lanes for today
- keep dashboard consistent with current signal-depth language

**Success criteria**

- management can see whether opening/midday/closing work exists and is complete
- dashboard signals remain operational, not decorative

---

### WF1-D Template Administration Upgrade

**Goal**

Make admin governance match the new runtime truth.

**Scope**

- template index should communicate active state by scope
- template authoring should explain runtime target per scope
- duplication and activation language should stay safe and clear

**Success criteria**

- admin no longer interprets activation as global replacement
- template list reads like a governance board, not a flat list

---

### WF1-E Quality Hardening and Documentation

**Goal**

Close the wave with regression safety and canonical docs alignment.

**Scope**

- feature/unit/browser coverage for new scope runtime
- README and current state updates
- spec/data-definition/decision-log alignment if contracts changed

**Success criteria**

- no ambiguity remains between docs and implementation
- tests prove the new runtime invariant

---

## 6. Recommended Execution Order

1. **WF1-A Domain and Persistence Realignment**
2. **WF1-B Staff Runtime Board and Scope Entry**
3. **WF1-D Template Administration Upgrade**
4. **WF1-C Scope-Aware Dashboard and Signals**
5. **WF1-E Quality Hardening and Documentation**

### Why this order

- persistence truth must change first
- staff runtime must become real before dashboard summarizes it
- admin governance must match runtime before rollout feels safe
- dashboard should summarize reality, not precede it

---

## 7. Engineering Principles for WF1

1. Do not bolt scope behavior on top of the current singular runtime by conditionals alone.
2. Do not keep saying “scope is metadata” in some places and “scope is runtime” in others.
3. Keep application owners clear:
   - initialization logic owns run creation
   - template actions own activation invariants
   - dashboard query owns summary shaping
   - Livewire components own presentation and orchestration only
4. Prefer extending the current model over introducing parallel abstractions.
5. Preserve historical runs and existing incident handoff behavior.

---

## 8. Key Risks

### Risk 1: Half-migrated truth

If the database allows per-scope active templates but the UI still talks like activation is global, the product becomes misleading.

### Risk 2: Runtime duplication confusion

If scope entry and run creation are unclear, staff may not understand whether they already completed a scope today.

### Risk 3: Dashboard incoherence

If dashboard remains aggregate-only after runtime becomes scope-aware, management value will lag behind reality.

---

## 9. Expected Outcome

After WF1 completes, the product should feel like:

- a real daily operations board
- not one generic checklist form
- admin-governed by operational moment
- staff-readable by phase of day
- management-visible by actual work slice

This is the smallest next move that produces the biggest jump in perceived usefulness.

