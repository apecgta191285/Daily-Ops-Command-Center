# Frontend Design Contract

This document is the product-facing UI contract for the graduation-project build.
It keeps frontend work grounded in a stable direction instead of quick visual fixes.

## Direction

Daily Ops Command Center uses a calm operations-console interface:

- quiet, professional, and built for repeated internal use
- dense enough for scanning, never decorative for its own sake
- status color is reserved for meaning: blue for action, green for complete, yellow for attention, red for risk
- desktop is the current supported presentation target

## Page Hierarchy

Every authenticated surface should follow this order:

1. Compact page header: current job, one-sentence purpose, primary action.
2. Primary work area: table, checklist, form, queue, or decision board.
3. Supporting context: summaries, history, guidance, or secondary metrics.

Use a large `ops-hero` only when it materially helps orientation. Runtime work pages should prefer compact headers and put the user's task first.

## Component Rules

- `ops-card`: use for one coherent work surface, not for nesting decorative panels.
- `ops-section-heading`: use once per major section; avoid stacking multiple explanatory headings before a form or table.
- `ops-shell-chip`: use for short metadata only. Do not use chips as paragraph substitutes.
- `ops-button--primary`: one primary action per section.
- `ops-alert`: use for state that changes what the user can do next.
- Tables and forms should optimize scan speed over visual drama.

## QA Gate

Current frontend QA is desktop-first:

- `npm run build`
- `php artisan test`
- `composer test:browser:desktop`

Mobile browser confidence is intentionally outside the current scope until the desktop graduation demo is stable.

## Phase Order

1. Stabilize runtime and desktop QA.
2. Tighten shell, spacing, navigation, and component rules.
3. Redesign core workflows in order: daily checklist, incident creation, incident queue, dashboard, template authoring, user management.
4. Add desktop screenshot baselines only after the redesigned surface is intentionally stable.

## Phase 2 Checklist Runtime Notes

The daily checklist is the first workflow-first surface. Its UI should make answer selection, progress, and submission more prominent than explanatory copy:

- the checklist item row is the main unit of work
- selected answers must be visually obvious without reading the radio state
- progress and remaining count should stay scannable before submit
- the submit action should remain visible at the end of the work area

## Phase 3 Core Workflow Cohesion

The core desktop workflows now carry explicit `ops-screen` identities so the interface can be governed as one product instead of disconnected pages:

- staff: daily checklist and incident report
- management: dashboard, incident queue, incident detail, incident history, checklist archive, checklist recap
- admin: template index, template authoring, user roster, user provisioning/editing

Shared rules for these workflows:

- cards, tables, forms, history summaries, and admin governance panels use the same radius, padding, focus, and density language
- operational pages prefer compact headers and task-first work areas
- primary actions stay visually stable and reachable inside long forms
- desktop smoke coverage must include every core workflow family before the UI is considered ready for demo

Current QA evidence:

- `php artisan test`
- `composer test:browser:desktop`
- `PATH="$HOME/.local/codex-node-v22.20.0/bin:$PATH" npm run build`

## Phase 4 Premium Redesign Direction

The visible direction is now `University Lab Operations Desk`.
This phase intentionally changes the product look instead of only polishing the previous UI:

- authenticated pages use a light command masthead instead of repeating the same dark block at the top of every page
- the dark cockpit panel is reserved for high-orientation workflow summaries, not every text section
- surfaces use a premium laboratory desk language: cool off-white canvas, crisp blue action, restrained teal/amber status accents, deeper but controlled shadows
- tables, filters, cards, and form controls share the same radius, border weight, focus treatment, and scan density
- the public entry and login screens follow the same cockpit-and-desk visual system so the product no longer feels split between separate sites

The standard is not "changed CSS"; it is "a user can see the interface has a deliberate product identity from screenshots alone."
