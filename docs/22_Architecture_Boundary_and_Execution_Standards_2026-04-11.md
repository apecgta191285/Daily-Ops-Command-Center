# Architecture Boundary and Execution Standards

Date: 2026-04-11
Project: Daily Ops Command Center
Execution phase coverage:

- Phase 2: Architectural and Security Boundary Definition

Reference inputs:

- [00_Project_Lock_v1.1.md](./00_Project_Lock_v1.1.md)
- [02_System_Spec_v0.3.md](./02_System_Spec_v0.3.md)
- [05_Decision_Log_v1.3.md](./05_Decision_Log_v1.3.md)
- current repository state on 2026-04-11

## 1. Purpose

This document is the canonical architecture contract for the current repository baseline.

It defines:

- current ownership boundaries
- target placement rules for new code
- authoritative shell strategy
- explicit account lifecycle and authorization policy
- thin Livewire component standard

## 2. Current Feature Classification

The current repository can be classified as follows.

### Custom Livewire-owned

Scope:

- operational workflows and admin template management with domain-specific behavior

Current repository owner:

- staff checklist execution
- staff incident creation
- management incident list/detail/update
- management incident accountability and ownership pressure surfaces
- admin checklist template list/create/edit
- admin user roster and lifecycle management

Authoritative route family:

- `/templates`
- `/templates/create`
- `/templates/{template}/edit`
- `/users`
- `/users/create`
- `/users/{user}/edit`

Current code locations:

- `app/Livewire/Staff/**`
- `app/Livewire/Management/**`
- `app/Livewire/Admin/**`
- `resources/views/livewire/**`
- `resources/views/dashboard.blade.php`
- `app/Http/Controllers/Management/DashboardController.php`

Decision:

- custom Livewire remains the target owner for the operations product and admin presentation surface
- future application-layer extraction will happen behind these flows rather than replacing them wholesale
- the current daily checklist runtime is now scope-aware; template `scope` is part of runtime truth and not just classification metadata
- legacy `/admin/*` checklist-template URLs are retired and must not be reintroduced as compatibility aliases without an explicit new decision

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
- current refactor priority is to restyle and constrain this surface through app-owned frontend contracts before considering a full migration to explicit Livewire classes

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

- HTTP, Livewire, Blade, Flux, route composition

Target locations:

- `app/Http/**`
- `app/Livewire/**`
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
- auth/settings shell work should converge through shared app-owned CSS modules and composition primitives
- full migration of settings pages to explicit Livewire classes is deferred until there is a stronger payoff than the current modular-contract work

## 5. Account Lifecycle and Authorization Policy

This section is the canonical policy for the current remediation cycle.

### Account lifecycle policy

- users must be active to authenticate into the operations application
- users must remain active to access protected application routes
- template-administration access requires both:
  - active account state
  - authorized admin role
- user-administration access requires both:
  - active account state
  - authorized admin role
- user lifecycle guard rails require:
  - self-deactivation must be rejected
  - self-demotion out of the admin role must be rejected
  - the system must retain at least one active admin account
- incident accountability updates require:
  - active authenticated user
  - management-capable role
  - selected owner, when present, must also be management-capable
- public self-registration is intentionally unsupported

### Authorization placement policy

Current baseline:

- route-level role middleware is the authoritative authorization layer for checklist template administration
- no general object-level policy layer is required yet because template actions remain admin-only and incident ownership currently affects accountability semantics, not per-record view permissions

Escalation trigger:

- introduce policy-based authorization only when template actions diverge by action type, ownership, or record-sensitive business rules, or when incident ownership becomes a true access boundary instead of a coordination signal

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
- checklist template administration is an admin-only surface inside the shared application shell

### Enforcement ownership

- login-time account eligibility belongs in authentication configuration
- protected-route account-state enforcement belongs in middleware
- role-specific route access belongs in dedicated role middleware
- current incident accountability semantics belong in application actions and support policy owners, not in Blade conditionals
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
- single-surface admin strategy

Conclusion:

Phase 2 decision artifacts now exist and are suitable for guiding Phase 3 work.

Current note after WF3:

- admin-owned user lifecycle is now a first-class custom Livewire surface and follows the same thin-component/application-owner rule as other product workflows
