# Architecture Boundary and Execution Standards

Date: 2026-04-11
Project: Daily Ops Command Center
Execution phase coverage:

- Phase 2: Architectural and Security Boundary Definition

Reference inputs:

- [17_Codebase_Audit_Report_2026-04-10.md](./17_Codebase_Audit_Report_2026-04-10.md)
- [18_Foundation_Remediation_Plan_2026-04-11.md](./18_Foundation_Remediation_Plan_2026-04-11.md)
- [19_Execute_Preparation_Pack_2026-04-11.md](./19_Execute_Preparation_Pack_2026-04-11.md)
- current repository state on 2026-04-11

## 1. Purpose

This document converts the Phase 2 planning decisions into execution-grade repository standards.

It defines:

- current ownership boundaries
- target placement rules for new code
- authoritative shell strategy
- explicit account lifecycle and authorization policy
- thin Livewire component standard

## 2. Current Feature Classification

The current repository can be classified as follows.

### Filament-owned

Scope:

- internal admin CRUD only

Current repository owner:

- `ChecklistTemplateResource`

Current code locations:

- `app/Filament/Resources/**`
- `app/Providers/Filament/AdminPanelProvider.php`

Role boundary:

- management users only
- panel path remains `/admin`

Decision:

- Filament remains the owner for low-code administrative CRUD
- Filament is not the target host for core staff workflow screens

### Custom Livewire-owned

Scope:

- operational workflows with domain-specific behavior

Current repository owner:

- staff checklist execution
- staff incident creation
- management incident list/detail/update

Current code locations:

- `app/Livewire/Staff/**`
- `app/Livewire/Management/**`
- `resources/views/livewire/**`
- `resources/views/dashboard.blade.php`
- `app/Http/Controllers/Management/DashboardController.php`

Decision:

- custom Livewire remains the target owner for the operations product
- future application-layer extraction will happen behind these flows rather than replacing them wholesale

### Volt / Flux page-owned

Scope:

- auth and settings experience only

Current repository owner:

- login
- password reset
- email verification
- settings profile/security/appearance

Current code locations:

- `resources/views/pages/auth/**`
- `resources/views/pages/settings/**`
- `routes/settings.php`
- `app/Providers/FortifyServiceProvider.php`

Decision:

- Volt/Flux pages remain temporarily limited to auth/settings
- no business workflow screens are to be added under this pattern

### Shared shell / layout-owned

Scope:

- shared navigation
- app shell chrome
- auth shell chrome
- shared UI composition primitives

Current code locations:

- `resources/views/layouts/**`
- `resources/views/components/**`
- `resources/views/partials/**`

Decision:

- shell work must converge toward one authoritative app shell and one authoritative auth/settings shell strategy
- duplicate navigation structures remain remediation targets in later phases

## 3. Target Directory and Namespace Conventions

The repository will use pragmatic layered placement.

### Domain layer

Purpose:

- business vocabulary
- invariants
- domain rules that are not presentation-specific

Target locations:

- `app/Domain/<Context>/Enums`
- `app/Domain/<Context>/Rules`
- `app/Domain/<Context>/Policies`
- `app/Domain/<Context>/Services` only when the logic is truly domain-oriented rather than orchestration

Examples for upcoming work:

- incident status enums
- incident transition rules
- checklist result enums
- role and account-state concepts

### Application layer

Purpose:

- workflow orchestration
- use cases
- transaction boundaries
- coordination between models and domain rules

Target locations:

- `app/Application/<Context>/Actions`
- `app/Application/<Context>/Queries`
- `app/Application/<Context>/DTOs`

Examples for upcoming work:

- submit daily checklist run
- create incident
- update incident status
- load dashboard metrics

### Presentation layer

Purpose:

- HTTP, Livewire, Blade, Filament, Flux, route composition

Target locations:

- `app/Http/**`
- `app/Livewire/**`
- `app/Filament/**`
- `resources/views/**`
- `routes/**`

Rule:

- presentation code may validate request shape and map view state
- presentation code must not become the long-term home of business workflow orchestration

### Infrastructure / persistence-adjacent layer

Purpose:

- framework integration
- providers
- persistence-specific adapters when needed

Target locations:

- `app/Providers/**`
- Eloquent models remain in `app/Models/**`

Rule:

- Eloquent models remain the persistence boundary for now
- do not introduce repository-pattern indirection unless it removes demonstrable complexity

## 4. Authoritative Shell Model

### App shell decision

Authoritative app shell:

- `resources/views/layouts/app.blade.php`
- supporting app navigation partials under `resources/views/layouts/app/**`

Rules:

- all operations-facing screens must converge on the same app shell
- duplicated navigation logic is allowed temporarily only where already present
- later consolidation must remove redundancy between header/sidebar implementations without introducing a new shell system

### Auth and settings shell decision

Authoritative auth/settings strategy:

- keep current Flux/Volt-style auth/settings shell family temporarily
- do not spread that pattern into operations screens

Rules:

- auth/settings may continue to use existing page/layout conventions
- auth/settings shell work is maintenance-only until a dedicated consolidation phase

## 5. Account Lifecycle and Authorization Policy

This section is the canonical policy for the current remediation cycle.

### Account lifecycle policy

- users must be active to authenticate into the operations application
- users must remain active to access protected application routes
- management-panel access requires both:
  - active account state
  - authorized management role
- public self-registration is intentionally unsupported

### Verification policy

- email verification remains enabled as an account capability
- operational routes are not globally gated by `verified`
- settings routes that change account/security posture may require `verified`

Reason:

- the current product is internally provisioned, not public-signup driven
- requiring global verification for all ops routes would be a separate product decision and is not assumed silently during remediation

### Authorization boundary policy

- authentication answers who the user is
- middleware and policy-style checks answer whether the authenticated user may proceed
- role checks are not sufficient by themselves
- active/inactive state is a first-class authorization gate for protected application access

### Enforcement ownership

- login-time account eligibility belongs in authentication configuration
- protected-route account-state enforcement belongs in middleware
- role-specific route access belongs in dedicated role middleware
- future domain-specific action permissions belong in application/domain policy work, not in Blade conditionals

## 6. Thin Livewire Component Standard

Livewire components are presentation adapters, not workflow owners.

Allowed responsibilities:

- receive UI input
- maintain local view state
- trigger an application action or query
- handle validation that is purely request-shape oriented
- return data to a view
- emit UI feedback messages

Disallowed long-term responsibilities:

- encode canonical business-state lists in multiple components
- own cross-entity workflow orchestration
- decide business transitions directly in UI code when that logic is domain-relevant
- perform multi-step persistence workflows inline when an application action should exist

Current components that should be treated as extraction candidates in later phases:

- `app/Livewire/Staff/Checklists/DailyRun.php`
- `app/Livewire/Staff/Incidents/Create.php`
- `app/Livewire/Management/Incidents/Show.php`
- `app/Http/Controllers/Management/DashboardController.php`

Execution rule from this point onward:

- any new substantial workflow logic must be placed in the target application/domain structure
- do not add more business orchestration into Livewire components while remediation is active

## 7. Phase 2 Exit Assessment

This document now makes explicit:

- the boundary map
- placement conventions
- shell authority
- account lifecycle policy
- thin-component standard

Conclusion:

Phase 2 decision artifacts now exist and are suitable for guiding Phase 3 work.
