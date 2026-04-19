# WF5 Dashboard Workboard Upgrade Master Plan

**Date:** 2026-04-19  
**Wave ID:** `WF5`  
**Status:** WF5 complete

## 1. Why WF5 Exists

After `WF1` through `WF4`, the repository now has real product truth for:

- scoped daily checklist runtime
- incident ownership and overdue follow-up pressure
- user administration
- operational history

The dashboard is no longer fake.
But it is still mostly a command summary.

It is not yet a true "today's workboard".

WF5 exists to promote the dashboard from:

- a place that summarizes system state

into:

- a place that helps management decide what needs attention today

without turning the homepage into analytics theatre or a duplicate queue.

## 2. Product Goal

When WF5 is complete, a management user opening `/dashboard` should quickly understand:

- which checklist scope lanes are still incomplete today
- whether ownership pressure is rising right now
- which part of the operating day needs attention first
- what can be drilled into next without leaving the product language

## 3. What WF5 Is Not

WF5 will **not** introduce:

- historical analytics dashboards
- charts for their own sake
- report-builder behavior
- another incident queue inside the dashboard
- another checklist screen embedded inside the dashboard

WF5 stays intentionally thin:

- signal first
- action second
- no decorative density

## 4. Current Truth Before WF5

The repository already has the ingredients needed for a believable workboard:

- `WF1` provides scope-aware checklist runtime truth
- `WF2` provides ownership pressure truth
- `WF4` provides recent operational history truth

That means WF5 should not invent new storage or synthetic metrics first.

WF5 should compose what the product already knows into a better operating board.

## 5. Planned Phases

### WF5-A Workboard Framing and Pending-Lane Truth

**Goal**

Shift the dashboard from broad summary toward today-first operational framing.

**Scope**

- make pending scope lanes more explicit
- elevate "what still needs action today" above passive summaries
- introduce calmer empty-state logic when a day has no pressure
- keep the page readable and non-duplicative

**Success criteria**

- dashboard answers "what still needs action today?" immediately
- empty/calm state feels intentional rather than sparse

### WF5-B Ownership and Work Buckets

**Goal**

Make accountability and pending work read as actionable buckets rather than scattered signals.

**Scope**

- unresolved incidents by owner bucket
- overdue follow-up bucket
- unowned bucket
- thin drill-down paths into the live queue

**Success criteria**

- ownership pressure feels like operating language, not just dashboard decoration

### WF5-C History-Aware Command Layer

**Goal**

Use lightweight recent-history context to make today's board more believable.

**Scope**

- pull in thin recent-history cues where they improve decision confidence
- highlight whether today looks calm, behind, or recently unstable
- avoid turning the dashboard into a retrospective report center

**Success criteria**

- dashboard feels more like a control point for the current day
- history remains supportive context, not the main event

### WF5-D Quality Hardening and Documentation

**Goal**

Close WF5 with regression proof and canonical truth alignment.

**Scope**

- feature/browser proof for new dashboard workboard behavior
- README/current-state alignment
- canonical documentation updates if product truth changes materially

**Success criteria**

- dashboard workboard baseline is documented clearly
- code, tests, and docs speak one truth

**Status**

- complete

## 6. Execution Order

1. `WF5-A Workboard Framing and Pending-Lane Truth`
2. `WF5-B Ownership and Work Buckets`
3. `WF5-C History-Aware Command Layer`
4. `WF5-D Quality Hardening and Documentation`

## 7. Rules of Engagement

WF5 must follow these constraints:

- no quick dashboard-only hacks
- no metric invented without a current source of truth
- no duplicating queue/list behavior into the dashboard
- no over-expansion into analytics or BI
- no "beautiful but hollow" workboard UI

## 8. Expected Outcome

After WF5 completes, the product should feel like:

- a credible small operations product
- a dashboard that helps decide what to do now
- a system that is still realistic for a solo developer to maintain

That is the right next usefulness wave after WF4.

WF5 is now complete:

- `WF5-A` established today-first workboard framing
- `WF5-B` translated ownership pressure into real work buckets
- `WF5-C` added recent operational memory without crossing into analytics
- `WF5-D` aligned regression proof and canonical documentation
