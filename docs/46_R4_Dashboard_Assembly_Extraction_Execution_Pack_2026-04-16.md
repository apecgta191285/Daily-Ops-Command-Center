# R4 Dashboard Assembly Extraction Execution Pack

Date: 2026-04-16
Status: Executed

## Objective

Reduce the responsibility load inside `GetDashboardSnapshot` after the N5 dashboard wave by extracting attention-item shaping, trend shaping, and hotspot shaping into dedicated dashboard support classes.

## Why This Slice

N5 improved product value, but it also made the dashboard query service denser. This refactor keeps the current dashboard contract intact while improving maintainability and keeping room for future dashboard evolution.

## Scope

- Extract dashboard attention-item assembly into a dedicated support class
- Extract dashboard trend shaping into a dedicated support class
- Extract dashboard hotspot summary shaping into a dedicated support class
- Add focused unit coverage for the new dashboard support classes
- Update canonical docs so the repository truth reflects the refactor

## Decisions

- Do not change dashboard behavior or route contract
- Keep raw metric querying inside `GetDashboardSnapshot`
- Move presentation-ready summary shaping out of the query service
- Keep the refactor lightweight and avoid introducing analytics infrastructure or repository pattern abstractions

## Acceptance Criteria

- `GetDashboardSnapshot` is thinner and no longer owns attention/trend/hotspot shaping directly
- Dashboard behavior remains unchanged for management users
- New support classes have focused unit coverage
- Existing dashboard feature and browser tests remain green

## Verification

- `composer lint:check`
- `php artisan test`
- `composer test:browser`

## Outcome

The dashboard application layer is now structured more cleanly:

- raw dashboard metrics still come from one query service
- display-ready dashboard summaries have dedicated owners
- future dashboard work can grow without turning `GetDashboardSnapshot` into a giant orchestration file
