# Browser QA Baseline

This folder is the browser-level quality gate for the repo's core surfaces.

Current baseline:

- smoke checks for guest, staff, management, and admin flows
- axe-backed accessibility assertions through Pest Browser's `assertNoAccessibilityIssues()` on deterministic smoke surfaces
- screenshot baselines for stable entry surfaces plus selected authenticated review/runtime screens

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
