# Browser QA Baseline

This folder is the browser-level quality gate for the repo's core surfaces.

Current desktop baseline:

- smoke checks for guest, staff, management, and admin flows
- axe-backed accessibility assertions through Pest Browser's `assertNoAccessibilityIssues()` on deterministic guest and admin-governance surfaces
- accessibility-backed smoke checks on selected management heavy screens where render state is stable enough for repeatable audits
- screenshot baselines for stable desktop entry surfaces plus selected authenticated dashboard and checklist runtime screens
- authenticated authoring and incident-detail screens currently stay under smoke coverage only until their render state is deterministic enough for stable screenshot gating

Coverage shorthand:

- `smoke only` = no JS errors, no console noise, core content present
- `smoke + accessibility` = smoke plus `assertNoAccessibilityIssues()`
- `screenshot-locked` = smoke plus `assertScreenshotMatches()`

Heavy-screen coverage stance right now:

- dashboard = `screenshot-locked`
- checklist runtime = `screenshot-locked`
- incident queue = `smoke + accessibility`
- incident history = `smoke + accessibility`
- checklist archive recap flow = `smoke + accessibility`
- template authoring = `smoke only`
- incident detail = `smoke only`

Snapshot update rule:

- update snapshots only when the UI change is intentional
- review desktop variants before accepting new baselines
- mobile coverage is intentionally outside the current graduation-project QA gate
- do not refresh snapshots to hide layout regressions, copy drift, or accessibility breakage

Practical workflow:

```bash
composer test:browser
composer test:browser:desktop
npm run test:browser:desktop
```

`composer test:browser` intentionally points at the desktop graduation gate. The older
full browser suite remains available as `composer test:browser:full` while mobile and
legacy screenshot baselines are outside the current scope.

If a snapshot changes in the legacy full suite, verify the diff first and only keep it
when the new render is the desired product truth.

Honesty rule:

- do not describe a surface as screenshot-locked unless the test actually calls `assertScreenshotMatches()`
- do not describe a surface as accessibility-gated unless the test actually calls `assertNoAccessibilityIssues()`
