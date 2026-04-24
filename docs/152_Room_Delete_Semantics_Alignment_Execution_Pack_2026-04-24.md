# Priority 7 - Room Delete Semantics Alignment

Date: 2026-04-24

## Why this round happened

The product now treats room context as mandatory domain truth for checklist runs and incidents. However, the foreign keys created during the original room schema slice still used `nullOnDelete()`. That meant a room deletion could silently break historical truth by nulling `room_id` even though the application and database now reject room-less runs and incidents.

This round aligned database delete semantics with the current room-centered model.

## Files changed

- `database/migrations/2026_04_24_000003_align_room_delete_semantics.php`
- `tests/Feature/Application/RoomDeleteSemanticsTest.php`

## What changed

- `checklist_runs.room_id` now uses `restrictOnDelete()`
- `incidents.room_id` now uses `restrictOnDelete()`
- deleting a room that is still referenced by checklist history or incident history is now rejected by the database
- deleting an unused room is still allowed

## Why this is correct

- historical checklist evidence and incident evidence should not lose room context
- room-centered invariants should be enforced consistently at the storage layer, not only in application code
- the repo still does not expose room administration or deletion workflows, so this is an integrity alignment pass rather than a UI feature wave

## What stayed intentionally unchanged

- no new room CRUD surface
- no archive backfill wave
- no dashboard or history UI changes
- no machine-registry drift

## Residual debt after this round

- legacy private/public attachment backfill still remains as a separate concern
- the repo still does not include a full room administration workflow, which is fine because this round was about protecting truth, not expanding scope
