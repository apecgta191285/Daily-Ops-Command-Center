# N5 Dashboard Trend and Hotspot Layer Execution Pack

Date: 2026-04-16
Status: Executed

## Objective

Strengthen the management dashboard so it does more than show current totals. The dashboard should now help management compare today against yesterday and quickly identify which incident categories are carrying the most unresolved pressure.

## Why This Slice

After F1-F5 and N1-N4, the product no longer suffers from foundation problems, but it still risks feeling shallow when a management user opens the dashboard. A small trend-and-hotspot layer is a high-value upgrade because it improves decision support without introducing analytics infrastructure, reporting pipelines, or schema-heavy complexity.

## Scope

- Add checklist completion trend using today versus yesterday
- Add incident intake trend using today versus yesterday
- Add hotspot summaries for unresolved incident categories
- Add dashboard drill-down links from hotspot summaries into filtered incident lists
- Add browser and feature regression coverage for the new dashboard layer

## Decisions

- No new analytics tables or background jobs are introduced
- Trend scope is intentionally limited to `today vs yesterday`
- Hotspot summaries are based on live unresolved incident data
- Stale counts inside hotspots reuse the shared incident stale policy owner

## Acceptance Criteria

- Dashboard renders `Checklist Trend`
- Dashboard renders `Incident Intake Trend`
- Dashboard renders `Operational Hotspots`
- Trend copy reflects actual delta direction and magnitude
- Hotspot rows expose unresolved count, stale count, and drill-down URL
- Feature tests cover trend and hotspot rendering
- Browser smoke confirms the new dashboard sections render without client-side regressions

## Verification

- `composer lint:check`
- `php artisan test`
- `composer test:browser`

## Outcome

The management dashboard now behaves more like a decision-support surface:

- checklist performance is contextualized against yesterday
- incident intake pressure is contextualized against yesterday
- workload concentration by category is visible without opening the incident list first

This remains intentionally lightweight and avoids over-engineering while still making the product feel more complete and purposeful.
