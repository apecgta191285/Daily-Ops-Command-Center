# WF5-A Workboard Framing and Pending-Lane Truth Execution Pack

**Date:** 2026-04-19  
**Wave:** `WF5 Dashboard Workboard Upgrade`  
**Parent Plan:** `95_WF5_Dashboard_Workboard_Upgrade_Master_Plan_2026-04-19.md`

## 1. Why WF5-A Exists

The dashboard already has:

- scope-lane signals
- accountability signals
- trend and hotspot support

What it still lacks is a stronger answer to the most important management question:

> what still needs action today?

WF5-A is the smallest correct step that upgrades the dashboard into a more believable workboard without jumping into bucket sprawl or pseudo-analytics.

## 2. Scope

WF5-A includes:

- stronger framing around pending checklist lane truth
- a clearer "today needs attention here" section
- calmer empty-state logic when the day is fully covered or quiet
- composition changes that improve action-first scanning

WF5-A does **not** include:

- owner buckets yet
- historical context layering yet
- new persistence or dashboard-specific data tables

## 3. Product Truth After WF5-A

After this phase:

- the dashboard becomes more explicitly today-oriented
- incomplete scope lanes are more prominent
- calm days read as intentionally calm, not empty
- the page stays a workboard, not a queue clone

## 4. Implementation Direction

WF5-A should prefer:

- reuse of existing dashboard query truth
- thin new support owners only where the framing logic truly needs one
- app-owned dashboard composition instead of ad hoc Blade conditionals

WF5-A should avoid:

- one-off template logic inside the Blade view
- hardcoded pseudo-priority copy with no data basis
- adding more cards just to make the page feel busier

## 5. Acceptance Markers

WF5-A is complete when:

- management can scan pending work lanes quickly from `/dashboard`
- calm days render a deliberate low-pressure state
- dashboard still drills into real product surfaces instead of dead ends
- regression proof covers the new framing behavior

## 6. Next Correct Step

After WF5-A, the next correct step is:

- `WF5-B Ownership and Work Buckets`

That is the point where dashboard pressure becomes more actionable without turning the page into a duplicate incident board.
