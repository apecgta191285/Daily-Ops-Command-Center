# WF1-A Domain and Runtime Realignment Execution Pack

**Date:** 2026-04-18  
**Status:** Ready for execution  
**Parent Plan:** WF1 Scoped Daily Operations Runtime

---

## 1. Objective

Make `ChecklistScope` operationally real by changing the system from:

- one active template globally

to:

- one active template per scope

This is the minimum contract change required before the staff runtime board and scope-aware dashboard can exist honestly.

---

## 2. Problem Statement

The current implementation mixes two truths:

- the product language suggests `opening / midday / closing`
- the runtime only supports one active checklist template at a time

As a result:

- `scope` is mostly administrative metadata
- the product feels thinner than the UI suggests
- template activation semantics are stronger than the real operating model

This pack corrects that mismatch at the correct layer first: persistence and application rules.

---

## 3. Scope of Change

### Included

- database invariant for active template uniqueness
- template activation behavior
- template activation messaging
- tests covering per-scope active-template truth
- canonical documentation updates where contract changes

### Not included

- staff scope board UI
- dashboard scope summary
- run history/archive features
- assignment/ownership work

---

## 4. Target Contract

### Before

- at most one active template exists in the whole system

### After

- at most one active template exists **per scope**
- examples:
  - one active `OPENING`
  - one active `MIDDAY`
  - one active `CLOSING`

### Activation semantics

When an admin saves a template as active:

- only the active template in the **same scope** is retired
- active templates in other scopes remain untouched

---

## 5. Likely Files to Change

### Persistence and model layer

- `database/migrations/2026_04_11_000008_harden_checklist_template_invariants.php`
- potentially a new migration replacing the old invariant with scope-aware uniqueness
- `app/Models/ChecklistTemplate.php`

### Application and support layer

- `app/Application/ChecklistTemplates/Actions/SaveChecklistTemplate.php`
- `app/Application/ChecklistTemplates/Support/TemplateActivationImpactBuilder.php`

### Livewire / admin surfaces

- `app/Livewire/Admin/ChecklistTemplates/Manage.php`
- `resources/views/livewire/admin/checklist-templates/index.blade.php`
- `resources/views/livewire/admin/checklist-templates/manage.blade.php`

### Tests

- `tests/Feature/AdminSurfaceBoundaryTest.php`
- `tests/Feature/Application/InitializeDailyRunActionTest.php`
- any supporting scenario helpers impacted by active-template assumptions

### Docs

- `README.md`
- `docs/04_Current_State_v1.3.md`
- possibly `docs/02_System_Spec_v0.3.md`
- possibly `docs/06_Data_Definition_v1.2.md`

---

## 6. Implementation Strategy

### Step 1. Replace the active-template invariant

Current invariant is global-single-active.

We need a scope-aware invariant:

- unique active template where `is_active = true` within each `scope`

For SQLite, that likely means replacing the current partial unique index with a new partial unique index over scope where `is_active = 1`.

### Step 2. Update application save behavior

`SaveChecklistTemplate` should retire only templates that are:

- active
- in the same scope
- not the current template

It must stop retiring unrelated scopes.

### Step 3. Update activation messaging

All template governance messaging must change from:

- "replace the current live checklist"

to:

- "replace the current live checklist for this scope"

### Step 4. Re-baseline tests

Add proofs for:

- allowing active templates in different scopes
- forbidding multiple active templates in the same scope
- ensuring same-scope activation retires only same-scope live template

---

## 7. Acceptance Criteria

### Product criteria

- admin can keep one active opening template and one active closing template at the same time
- activating a midday template does not retire opening/closing templates
- template management copy explains scope-local activation truth clearly

### Engineering criteria

- persistence invariant enforces same-scope uniqueness
- no logic for active-template retirement remains global by mistake
- tests prove both allowed and forbidden cases
- docs no longer describe scope as metadata-only after this pack lands

---

## 8. Regression Concerns

### Checklist initialization

Current initialization assumes one active template.

WF1-A should not fake multi-scope runtime yet, but it must not leave initialization in an ambiguous state.

Safe interim rule:

- current singular runtime remains unchanged until WF1-B
- WF1-A only changes template activation truth and admin-side governance

That means staff runtime must not silently pick among multiple active templates after this step unless we also land the necessary selection logic in the same round.

### Important note

Because of that risk, WF1-A may need one of these two safe paths:

1. land with a temporary runtime guard that keeps `/checklists/runs/today` limited to a chosen scope until WF1-B lands immediately after
2. land WF1-A and WF1-B in one controlled implementation round

For this project, option 2 is cleaner.

---

## 9. Recommended Execution Decision

### Brutal truth

WF1-A should **not** be implemented as an isolated persistence-only change and then left in production-like state for long.

The cleanest approach is:

- design WF1-A first
- implement WF1-A together with the minimum runtime entry changes from WF1-B in the same feature round

That avoids a half-true system.

---

## 10. Definition of Done

WF1-A is done only when:

- same-scope active-template invariants are correct
- template activation behavior is scope-local
- admin messaging reflects scope-local truth
- tests cover the new invariant
- runtime ambiguity is avoided

If any one of these is missing, the round is incomplete.

