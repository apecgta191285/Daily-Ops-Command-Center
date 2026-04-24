# Priority 8 - Legacy Attachment Backfill

Date: 2026-04-24

## Why this round happened

New incident evidence uploads already land on private local storage and are served back through authenticated management-only routes. However, legacy evidence files created before that hardening round could still remain on the public disk.

That left a real privacy and integrity gap between the current upload path and older stored files.

## Files changed

- `app/Application/Incidents/Support/BackfillLegacyIncidentAttachments.php`
- `routes/console.php`
- `tests/Feature/Application/BackfillLegacyIncidentAttachmentsTest.php`

## What changed

- added a dedicated backfill owner that scans incidents with attachments and classifies each file as:
  - already private
  - legacy public
  - missing
- legacy public attachments are copied to the local private disk and removed from the public disk
- added an Artisan command:
  - `incident-attachments:backfill-private`
- added regression proof for:
  - public-to-private migration
  - idempotent no-op behavior for already-private files
  - command execution and summary reporting

## Why this is correct

- it closes the gap left after secure attachment handling changed only the forward upload path
- it preserves the current attachment path contract, so download routes and management UI do not need a parallel rewrite
- it gives the repo an explicit operational owner for evidence migration instead of leaving legacy files as indefinite debt

## What stayed intentionally unchanged

- no attachment schema expansion
- no cloud/object storage wave
- no attachment virus scanning or content moderation wave
- no browser/UI redesign work

## Residual debt after this round

- this is still a manually-invoked backfill path, not scheduled automation
- missing legacy attachment files are reported, not reconstructed
- a deeper production attachment hardening wave would still be needed for things like malware scanning or signed temporary URLs
