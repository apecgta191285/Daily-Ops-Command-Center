# Program 2 / Phase 2.4 - Domain Truth Hardening

## Scope

This pass hardens the application-layer invariants behind the room-centered workflow so the domain truth is not enforced only by Livewire forms.

## What Was Hardened

- `InitializeDailyRun` now requires the room dimension to be valid before creating or loading a run.
- `InitializeDailyRun` now returns:
  - `room_missing` when there are no active rooms
  - `room_required` when multiple active rooms exist but none was chosen
  - `room_required` when an explicit room is invalid or inactive
- `CreateIncident` now requires a valid active room instead of accepting nullable or inactive room references.

## Files Changed

- `app/Application/Checklists/Actions/InitializeDailyRun.php`
- `app/Application/Incidents/Actions/CreateIncident.php`
- `tests/Feature/Application/InitializeDailyRunActionTest.php`
- `tests/Feature/Application/CreateIncidentActionTest.php`

## What Was Intentionally Left Untouched

- No schema or migration changes
- No Livewire redesign
- No dashboard/query wave
- No machine registry work
- No room CRUD work

## Remaining Risk

- Checklist incident prefill still treats query-string context as convenience data; final trust remains in the incident creation flow and action validation.
- Further hardening beyond this point should be driven by concrete production paths, not speculative refactors.
