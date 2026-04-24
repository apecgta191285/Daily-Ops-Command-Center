# Secure Attachment Handling Execution Pack

Date: 2026-04-24

## Intent

Close the highest-priority production gap identified in the full-stack audit:

- stop creating new incident evidence files on the public disk
- stop exposing direct public `storage/...` URLs in management surfaces
- move attachment access behind authenticated management routes
- keep legacy public-disk attachments readable through the secure route during transition

## Why This Round Exists

Before this change:

- new uploads were stored on the `public` disk
- incident detail linked directly to `asset('storage/...')`
- print summary exposed the raw public attachment URL
- attachment validation allowed any file type so long as it fit the size limit

That behavior was acceptable for demo convenience but not for production-grade privacy or access control.

## Changes Landed

- new incident uploads now store to the `local` disk
- staff incident form now limits attachment types to `pdf`, `jpg`, `jpeg`, `png`, and `webp`
- management users open attachments through an authenticated route instead of a public asset URL
- incident detail surface now links to the secure attachment route
- printable summary no longer prints a raw public attachment URL
- legacy attachments that still live on the `public` disk can still be served through the secure route so current records do not break
- welcome mobile contrast was tightened after browser QA surfaced a real accessibility regression during this round's verification pass

## Intentionally Left Untouched

- no attachment migration/backfill job in this round
- no malware scanning
- no attachment metadata table
- no new policy layer beyond the existing management route boundary
- no redesign of incident detail or print surfaces

## Honest Limitation After This Round

- legacy files already published on the `public` disk are not retroactively made private by this change alone
- full closure will require a later migration or operational backfill to move legacy evidence off the public disk
- attachment access is now much safer in the live UI, but the historical storage footprint still needs cleanup if the target is strict production-grade privacy
