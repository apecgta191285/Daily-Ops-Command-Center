# 144. Room Context Truth Closure Execution Pack

Date: 2026-04-24

## Intent

Close the remaining mismatch between:

- room-centered product truth in application logic
- database schema guarantees
- factories and shared test helpers

This round stays narrow. It does not open a new feature wave.

## Changes Landed

1. Added a migration to enforce `room_id` as non-null for:
   - `checklist_runs`
   - `incidents`
2. Added migration preflight guards so the schema hardening stops if legacy room-less records still exist.
3. Updated factories so room-aware records default to a room instead of silently generating impossible room-less state.
4. Updated shared scenario helpers so they reuse an active room when possible and only create a room when none exists.
5. Added regression proof that the database now rejects:
   - room-less checklist runs
   - room-less incidents
6. Disabled the Livewire navigate progress bar at the layout layer because its runtime markup introduced an invalid ARIA role during browser accessibility checks.

## Why This Was Necessary

The application layer already enforced room-aware behavior, but the storage layer and test fixtures still allowed room-less records.

That created three problems:

- product truth was stronger than schema truth
- tests could still create impossible state
- future refactors could accidentally reintroduce room-less data

## Intentionally Left Untouched

- no route changes
- no UI redesign
- no query-wave rewrite
- no machine registry work
- no Option B work

## Closure Standard

This round is only successful if:

- room-aware truth exists in app logic
- room-aware truth exists in DB constraints
- room-aware truth exists in test fixtures
- verification remains green
