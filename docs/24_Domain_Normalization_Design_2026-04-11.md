# Domain Normalization Design

Date: 2026-04-11
Project: Daily Ops Command Center
Execution phase coverage:

- Phase 3: Domain and Invariant Normalization

Reference inputs:

- [19_Execute_Preparation_Pack_2026-04-11.md](./19_Execute_Preparation_Pack_2026-04-11.md)
- [22_Architecture_Boundary_and_Execution_Standards_2026-04-11.md](./22_Architecture_Boundary_and_Execution_Standards_2026-04-11.md)

## 1. Purpose

This document is the Phase 3 execution artifact.

It records:

- the current inventory of business-state literals
- the canonical domain types selected for those literals
- the invariant ownership model
- the data transition strategy
- the test update strategy for later extraction phases

## 2. Literal Inventory

### Roles

Canonical literal set:

- `admin`
- `supervisor`
- `staff`

Current literal sources:

- `app/Models/User.php`
- `routes/web.php`
- `database/migrations/2026_04_05_000001_add_role_and_is_active_to_users_table.php`
- `database/seeders/DatabaseSeeder.php`
- `database/factories/UserFactory.php`
- feature tests that query by role

### Incident statuses

Canonical literal set:

- `Open`
- `In Progress`
- `Resolved`

Current literal sources:

- `app/Livewire/Management/Incidents/Index.php`
- `app/Livewire/Management/Incidents/Show.php`
- `app/Livewire/Staff/Incidents/Create.php`
- `app/Http/Controllers/Management/DashboardController.php`
- `database/migrations/2026_04_05_000006_create_incidents_table.php`
- `database/seeders/DatabaseSeeder.php`
- feature tests and Blade badge mappings

### Incident categories

Canonical literal set:

- `อุปกรณ์คอมพิวเตอร์`
- `เครือข่าย`
- `ความสะอาด`
- `ความปลอดภัย`
- `สภาพแวดล้อม`
- `อื่น ๆ`

Current literal sources:

- `app/Livewire/Management/Incidents/Index.php`
- `app/Livewire/Staff/Incidents/Create.php`
- `database/migrations/2026_04_05_000006_create_incidents_table.php`
- `database/seeders/DatabaseSeeder.php`
- feature tests

### Incident severities

Canonical literal set:

- `Low`
- `Medium`
- `High`

Current literal sources:

- `app/Livewire/Management/Incidents/Index.php`
- `app/Livewire/Staff/Incidents/Create.php`
- `database/migrations/2026_04_05_000006_create_incidents_table.php`
- `database/seeders/DatabaseSeeder.php`
- feature tests
- Blade badge mappings

### Checklist results

Canonical literal set:

- `Done`
- `Not Done`

Current literal sources:

- `app/Livewire/Staff/Checklists/DailyRun.php`
- `database/migrations/2026_04_05_000005_create_checklist_run_items_table.php`
- `database/seeders/DatabaseSeeder.php`
- `resources/views/livewire/staff/checklists/daily-run.blade.php`
- feature tests

## 3. Canonical Domain Types

Canonical domain types have now been defined in code:

- `App\Domain\Access\Enums\UserRole`
- `App\Domain\Incidents\Enums\IncidentStatus`
- `App\Domain\Incidents\Enums\IncidentCategory`
- `App\Domain\Incidents\Enums\IncidentSeverity`
- `App\Domain\Checklists\Enums\ChecklistResult`

Decision:

- enum backing values must match currently stored database string values exactly during the normalization and extraction phases

Reason:

- this avoids unnecessary data churn before workflow extraction is complete
- the first remediation step is centralization, not semantic renaming

## 4. Invariant Ownership Map

### Schema-owned invariants

These belong at the database/schema level:

- required foreign keys
- required columns
- default values such as:
  - user role default `staff`
  - incident status default `Open`
  - active-state default `true`
- timestamp storage for:
  - `submitted_at`
  - `resolved_at`

### Domain-owned invariants

These belong in canonical domain definitions:

- allowed roles
- allowed incident statuses
- allowed incident categories
- allowed incident severities
- allowed checklist results
- which transitions are conceptually valid between incident states

### Application-owned invariants

These belong in use-case orchestration:

- creating or loading today's checklist run
- enforcing exactly one active checklist template for daily execution
- setting checklist submission timestamps and actors
- setting incident resolution timestamp when status becomes resolved
- clearing incident resolution timestamp when status leaves resolved
- writing incident activity trail on create and status transitions

### Presentation-owned constraints

These remain allowed only at the input-shape or display level:

- request field presence
- route composition
- layout selection
- translating canonical domain values into badge colors and labels

Presentation must not own:

- the source of truth for business-state lists
- long-term transition rules
- persistence orchestration

## 5. Resolved Invariant Examples

### Exactly one active checklist template

Current state:

- enforced ad hoc inside `DailyRun`

Target ownership:

- application layer orchestrates active-template resolution
- domain layer provides canonical meaning of active/inactive template state

### Incident status transitions

Current state:

- transition behavior exists inline in `Management/Incidents/Show`

Target ownership:

- domain enum defines state language
- application action owns transition execution and side effects

### Checklist completion rules

Current state:

- completion validation exists inline in `Staff/Checklists/DailyRun`

Target ownership:

- domain enum defines allowed result values
- application action owns submission rule enforcement and persistence

## 6. Data Transition and Migration Strategy

Decision:

- no database migration is required in Phase 3 for enum introduction alone

Phase 3 approach:

- introduce canonical enum classes first
- preserve current stored string values
- do not rename persisted values yet

Future migration rule:

- if a later phase changes a stored value, database column type, or constraint shape, that later phase must include:
  - forward migration
  - backfill strategy
  - rollback note

Current backfill requirement:

- none

Current rollback concern:

- low, because Phase 3 only centralizes definitions without altering persisted data

## 7. Test Update Strategy

Short-term test strategy:

- keep current feature tests behavior-focused
- allow existing literal assertions temporarily while canonical types are being introduced

Next-step strategy for Phase 4 and beyond:

- add service/application-layer tests that use canonical enums as the primary contract
- reduce direct dependence on scattered literal arrays in UI tests
- add transition tests around incident status rules
- add tests around active-template resolution and checklist submission orchestration

## 8. Phase 3 Exit Assessment

Phase 3 checklist status:

- business-state literals inventoried
- canonical types defined
- invariant ownership map defined
- migration strategy defined
- test update strategy defined

Conclusion:

- Phase 3 execution artifacts now exist
- Phase 4 can begin without re-deciding business vocabulary
