# Browser QA Baseline

This folder is the browser-level quality gate for the repo's core surfaces.

Current baseline:

- smoke checks for guest, staff, management, and admin flows
- axe-backed accessibility assertions through Pest Browser's `assertNoAccessibilityIssues()` on deterministic guest and admin-governance surfaces
- screenshot baselines for stable entry surfaces plus selected authenticated dashboard and checklist runtime screens
- authenticated authoring and incident-detail screens currently stay under smoke coverage only until their render state is deterministic enough for stable screenshot gating

Snapshot update rule:

- update snapshots only when the UI change is intentional
- review both desktop and mobile variants before accepting new baselines
- do not refresh snapshots to hide layout regressions, copy drift, or accessibility breakage

Practical workflow:

```bash
./vendor/bin/pest tests/Browser --update-snapshots
composer test:browser
```

If a snapshot changes, verify the diff first and only keep it when the new render is the desired product truth.

Honesty rule:

- do not describe a surface as screenshot-locked unless the test actually calls `assertScreenshotMatches()`
- do not describe a surface as accessibility-gated unless the test actually calls `assertNoAccessibilityIssues()`
