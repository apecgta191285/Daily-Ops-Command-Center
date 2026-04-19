# WF4 Operational History and Run Archive Master Plan

**Date:** 2026-04-19  
**Status:** Planning locked  
**Execution Standard:** No analytics monster, no fake archive UI, no history without operational meaning

---

## 1. Why WF4 Exists

The repository now supports:

- scoped daily runtime
- incident accountability
- user administration

That means the next credibility gap is no longer "can the team do the work?".

The gap is:

> can the team review what happened yesterday, last shift, or last week without reading raw tables or relying on seed-memory?

Right now the system is strong at:

- doing today's checklist
- reporting and triaging incidents
- scanning current pressure

But it is still weak at:

- reviewing completed runs by date
- comparing who completed what
- reopening operational context from past runs
- reading a thin incident history layer beyond the active queue

WF4 exists to make the product feel like a real operational record, not just a current-state app.

---

## 2. WF4 Product Goal

Turn retained data into a lightweight review surface that helps a small team answer:

- what happened on a given day
- which checklist lane was completed or missed
- who submitted the run
- what not-done answers were recorded
- which incidents were active or resolved around that time

The goal is not business intelligence.

The goal is:

> enough history to support review, follow-up, and confidence in the system as a record of work

---

## 3. What WF4 Is Not

WF4 will **not** introduce:

- analytics warehouse
- custom report builder
- pivot-table dashboards
- export-first reporting platform
- audit-trail engine for every model mutation
- infinite filtering vocabulary
- multi-surface archive maze

WF4 stays deliberately thin:

> history for operators and managers, not enterprise reporting

---

## 4. Current Truth Before WF4

The current repository already has:

- checklist runs persisted with dates, owner, submission state, and answers
- checklist scopes as real runtime lanes
- incident activity trail and accountability truth
- dashboard summary for current pressure

The current repository does **not** yet have:

- a first-class checklist history index
- a run recap archive page for management review
- one coherent history surface by date/scope/operator
- a thin historical management view that connects runs and incidents

That means WF4 is not inventing storage.

WF4 is productizing stored operational memory.

---

## 5. Target Product Model

### Current model before WF4

- checklist runs exist as runtime records
- recent context exists in some localized places
- management review is still biased toward "right now"

### Target model

- management can browse historical checklist runs by date
- history can be filtered by scope and operator
- each run has a readable recap view, not just raw answers
- the product exposes a thin historical narrative without becoming a reporting suite

### Product rules

1. WF4 remains review-oriented, not analytics-oriented.
2. History surfaces must reuse canonical scope vocabulary.
3. Archive views must not invent new business state beyond what runtime already records.
4. Run recap should prefer clear operational meaning over raw database completeness.
5. History filtering must stay small and believable:
   - date
   - scope
   - operator
6. WF4 must not turn dashboard into a report center.
7. If incident history is shown, it must remain thin and contextual.

---

## 6. Phase Map

### WF4-A Checklist Run Archive Core

**Goal**

Make checklist history browseable and reviewable as a first-class management capability.

**Scope**

- management history route family for checklist runs
- checklist run history query owner
- filter by date, scope, operator
- readable run archive index
- recap/detail surface for one historical run

**Success criteria**

- management can review past checklist runs without database access
- history uses real runtime data, not synthetic summary records
- the run detail surface reads like operational recap, not raw form replay

---

### WF4-B Historical Context and Cross-Linking

**Goal**

Make run archive feel connected to the rest of the product.

**Scope**

- link run recap to related incident follow-up entry points where context exists
- surface not-done emphasis and operator metadata more clearly
- add thin management cues for missing or absent coverage across dates

**Success criteria**

- archive becomes useful for review and follow-up, not just browsing
- management can move from "what happened?" to "what needs review?" without a reporting subsystem

---

### WF4-C Incident History Slice

**Goal**

Expose a thin historical incident layer that complements current queue work.

**Scope**

- resolved/opened summary slices by recent date range
- lightweight history framing tied to existing incident data
- no reassignment, SLA, or notification retrospective logic

**Success criteria**

- the product starts to feel like a system of record over time
- incident history remains intentionally lightweight

---

### WF4-D Quality Hardening and Documentation

**Goal**

Close WF4 with regression proof and canonical truth alignment.

**Scope**

- feature and browser coverage for history surfaces
- canonical doc updates where operational history becomes first-class truth
- README/current-state alignment

**Success criteria**

- history workflow is documented clearly
- code, tests, and docs speak one truth

---

## 7. Recommended Execution Order

1. **WF4-A Checklist Run Archive Core**  
   First because it delivers the highest usefulness gain with the least conceptual sprawl.

2. **WF4-B Historical Context and Cross-Linking**  
   Second because archive should become actionable, not just viewable.

3. **WF4-C Incident History Slice**  
   Third because incident history is useful, but less foundational than checklist run archive.

4. **WF4-D Quality Hardening and Documentation**  
   Last, once history truth is stable.

---

## 8. Expected Outcome

After WF4 completes, the product should feel like:

- a system that helps the team operate today
- a system that helps management review what happened yesterday
- a system that retains believable operational memory without pretending to be enterprise BI

That is the smallest next wave that materially improves product usefulness without breaking solo-dev realism.
