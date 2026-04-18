# WF1-A + WF1-B Scope Runtime Entry Execution Pack

**Date:** 2026-04-18  
**Status:** Implemented  
**Parent Plan:** `71_WF1_Scoped_Daily_Operations_Runtime_Master_Plan_2026-04-18.md`

## Objective

Land the first real runtime slice of WF1 without leaving the product in an ambiguous half-state.

This round intentionally combined:

- `WF1-A` domain/persistence realignment
- the minimum safe runtime entry changes from `WF1-B`

so the system would not claim "one active template per scope" in admin while staff runtime still behaved as a single global lane.

## What Changed

### 1. Scope became a real activation boundary

- database uniqueness moved from `one active template globally` to `one active template per scope`
- template activation now retires only the currently active template inside the same scope
- activation impact messaging now explains scope-local replacement instead of global replacement

## 2. Staff runtime now supports scope-aware entry

- `/checklists/runs/today/{scope?}` now accepts an optional scope route key
- when exactly one live scope exists, staff still auto-enter the run without extra friction
- when multiple live scopes exist, staff now see a runtime board instead of a configuration failure
- when a specific scope has no active template, staff now get a scope-missing state instead of a misleading generic error

## 3. Scope board established a real daily operating model

- the daily runtime board now shows the three operating lanes:
  - `Opening`
  - `Midday`
  - `Closing`
- each lane reports:
  - template title
  - state
  - answered/total progress
  - completion percentage
- lane state now reads as one of:
  - `unavailable`
  - `not_started`
  - `in_progress`
  - `submitted`

## 4. Checklist-to-incident handoff now preserves scope

- incident prefill links now append `checklist_scope`
- staff incident create surface now returns to the correct checklist lane
- post-submit outcome flow preserves the same runtime context

## 5. Admin copy now matches runtime truth

- template index no longer claims one live template governs the whole system
- template manage no longer claims activation retires every other template
- welcome/demo copy now reflects scope-owned runtime instead of a single global checklist lane

## Files of Interest

- `app/Domain/Checklists/Enums/ChecklistScope.php`
- `database/migrations/2026_04_18_000010_scope_active_checklist_templates.php`
- `app/Application/ChecklistTemplates/Actions/SaveChecklistTemplate.php`
- `app/Application/ChecklistTemplates/Support/TemplateActivationImpactBuilder.php`
- `app/Application/Checklists/Actions/InitializeDailyRun.php`
- `app/Application/Checklists/Queries/BuildDailyScopeBoard.php`
- `app/Livewire/Staff/Checklists/DailyRun.php`
- `app/Livewire/Staff/Incidents/Create.php`
- `resources/views/livewire/staff/checklists/daily-run.blade.php`
- `resources/views/livewire/staff/incidents/create.blade.php`
- `resources/views/livewire/admin/checklist-templates/index.blade.php`
- `resources/views/livewire/admin/checklist-templates/manage.blade.php`
- `routes/web.php`

## Verification

- `composer lint:check`
- `php artisan test`
- `npm run build`
- `composer test:browser`

## Result

This round closed the largest product-truth gap identified in the full-stack audit:

`ChecklistScope` is no longer admin-only metadata.

It now affects:

- persistence invariants
- template governance
- staff runtime entry
- daily run retrieval
- incident return flow

The product still remains A-lite and avoids enterprise workflow creep, but the daily operations model now reads as a real multi-moment routine instead of one flattened checklist for the whole day.
