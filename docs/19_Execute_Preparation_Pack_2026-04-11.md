# Execute Preparation Pack

Date: 2026-04-11
Project: Daily Ops Command Center
Planning basis:

- [17_Codebase_Audit_Report_2026-04-10.md](./17_Codebase_Audit_Report_2026-04-10.md)
- [18_Foundation_Remediation_Plan_2026-04-11.md](./18_Foundation_Remediation_Plan_2026-04-11.md)

Purpose:

This document converts the remediation plan into an execution-ready backlog. It is the handoff artifact between Plan and Execute.

It answers:

- what to do
- in what order
- with what dependencies
- with what expected impact
- with what verification criteria
- when the team is actually ready to begin systematic remediation

This is not a patch script.
This is not a quick-fix checklist.
This is a controlled execution pack for foundation remediation.

## 1. How To Use This Pack

Execution rule:

- finish tasks in sequence within each phase unless explicitly marked parallel-safe
- do not start a later phase until the previous phase gate is satisfied
- do not mix feature delivery into the remediation stream
- every phase must end with verification evidence

Task metadata used in this pack:

- `Order`: execution order inside the phase
- `Type`: decision, refactor, cleanup, governance, documentation, validation
- `Dependencies`: what must already be true
- `Impact`: expected effect on system quality
- `Owner`: suggested role, not a named person
- `Artifact`: what output must exist when done
- `Verification`: how to prove completion

## 2. Global Start Conditions

These must be true before Phase 1 begins:

1. Feature freeze is active for foundation work.
2. A dedicated remediation branch strategy exists.
3. The team agrees that CI truth and architecture consistency take priority over adding features.
4. The current audit and plan documents are accepted as the baseline.

If these are not true, do not start execution.

## 3. Phase 0: Governance Freeze

Objective:

Prevent further entropy before technical remediation starts.

### P0-T1. Declare remediation freeze

- Order: 1
- Type: governance
- Dependencies: none
- Impact: high
- Owner: tech lead / repository owner
- Artifact: written freeze note in project docs or issue tracker
- Verification:
  - feature work explicitly paused
  - team members know which changes are disallowed during remediation

### P0-T2. Define branch and review policy

- Order: 2
- Type: governance
- Dependencies: P0-T1
- Impact: medium
- Owner: tech lead
- Artifact:
  - branch naming convention
  - PR review expectation
  - merge policy for remediation work
- Verification:
  - policy is documented
  - no direct unreviewed remediation commits land on `main`

### P0-T3. Define evidence standard for each phase

- Order: 3
- Type: governance
- Dependencies: P0-T2
- Impact: medium
- Owner: senior engineer
- Artifact: verification template for phases
- Verification:
  - each phase has required outputs:
    - updated docs
    - test/CI evidence
    - regression notes

### Phase 0 exit gate

Phase 0 is complete when:

- freeze is active
- review policy is active
- evidence standard is agreed

## 4. Phase 1: Platform, Environment, and Build Truth

Objective:

Make platform support, environment bootstrap, and runtime setup truthful and reproducible.

### P1-T1. Lock the supported PHP baseline

- Order: 1
- Type: decision/refactor
- Dependencies: Phase 0 complete
- Impact: critical
- Owner: senior engineer
- Artifact:
  - updated `composer.json`
  - updated CI matrix
  - updated documentation
- Verification:
  - declared PHP minimum is 8.4
  - CI does not test unsupported PHP versions
  - no docs still claim broader support

### P1-T2. Normalize Composer and CI platform contract

- Order: 2
- Type: refactor
- Dependencies: P1-T1
- Impact: critical
- Owner: senior engineer
- Artifact:
  - synchronized `composer.json`
  - synchronized `composer.lock`
  - synchronized workflows
- Verification:
  - fresh `composer install` works on supported PHP
  - GitHub Actions passes on declared baseline
  - no hidden platform mismatch remains

### P1-T3. Normalize `.env.example` to the canonical local bootstrap profile

- Order: 3
- Type: refactor/documentation
- Dependencies: P1-T1
- Impact: high
- Owner: backend engineer
- Artifact:
  - corrected `.env.example`
- Verification:
  - `.env.example` matches chosen local default profile
  - no contradiction remains between `.env.example`, README, and actual local runtime

### P1-T4. Normalize README and setup instructions

- Order: 4
- Type: documentation
- Dependencies: P1-T3
- Impact: high
- Owner: backend engineer
- Artifact:
  - updated [README.md](../README.md)
- Verification:
  - clean clone setup steps are deterministic
  - no undocumented manual step remains

### P1-T5. Make storage/attachment runtime expectations explicit

- Order: 5
- Type: operability
- Dependencies: P1-T4
- Impact: high
- Owner: backend engineer
- Artifact:
  - documented storage-link requirement
  - verified attachment delivery contract
- Verification:
  - `public/storage` handling is documented
  - attachment upload and attachment viewing both work in a clean environment

### P1-T6. Document CI secrets and external setup requirements

- Order: 6
- Type: documentation
- Dependencies: P1-T2
- Impact: medium
- Owner: DevOps-minded engineer
- Artifact:
  - CI prerequisites section in docs
- Verification:
  - Flux credentials and any required secrets are documented
  - CI failure modes are understandable from docs

### Phase 1 verification checklist

- `composer.json`, lock file, and CI all agree on PHP 8.4 minimum
- GitHub Actions baseline is green
- `.env.example` matches the real local profile
- README bootstraps the app successfully from a clean clone
- attachment delivery contract is explicit and tested
- CI secret requirements are documented

### Phase 1 impact summary

- removes false platform claims
- removes onboarding ambiguity
- removes build/setup drift
- turns the repo into a truthful install target

### Phase 1 exit gate

Do not start Phase 2 until:

- CI is green on supported versions
- local bootstrap is reproducible
- runtime storage assumptions are documented and validated

## 5. Phase 2: Architectural and Security Boundary Definition

Objective:

Define ownership boundaries so refactoring can happen without creating a new inconsistent state.

### P2-T1. Write architecture boundary map

- Order: 1
- Type: decision/documentation
- Dependencies: Phase 1 complete
- Impact: critical
- Owner: senior engineer
- Artifact:
  - boundary map document or ADR
- Verification:
  - every current feature can be classified into:
    - Filament
    - custom Livewire
    - Volt
    - shared shell/layout

### P2-T2. Define target directory and namespace conventions

- Order: 2
- Type: decision/documentation
- Dependencies: P2-T1
- Impact: high
- Owner: senior engineer
- Artifact:
  - conventions for domain/application/presentation/infrastructure placement
- Verification:
  - new code placement is rule-driven, not intuition-driven

### P2-T3. Define the authoritative shell model

- Order: 3
- Type: decision
- Dependencies: P2-T1
- Impact: high
- Owner: frontend/full-stack engineer
- Artifact:
  - shell ownership decision
- Verification:
  - one target app shell is identified
  - one target auth/settings shell strategy is identified

### P2-T4. Define account lifecycle and authorization policy

- Order: 4
- Type: decision
- Dependencies: P2-T1
- Impact: critical
- Owner: senior engineer
- Artifact:
  - policy covering:
    - `is_active`
    - email verification expectations
    - registration policy
    - auth vs authorization boundaries
- Verification:
  - team can answer:
    - can inactive users log in?
    - can unverified users use ops routes?
    - is self-registration supported?
    - which layer owns enforcement?

### P2-T5. Define "thin Livewire component" standard

- Order: 5
- Type: decision/documentation
- Dependencies: P2-T2
- Impact: high
- Owner: senior engineer
- Artifact:
  - coding standard for component responsibility
- Verification:
  - standard explicitly prohibits business workflow orchestration living in UI components

### Phase 2 verification checklist

- architecture boundary map exists
- folder/namespace conventions exist
- shell authority is explicit
- authorization/account lifecycle policy is explicit
- thin-component standard exists

### Phase 2 impact summary

- prevents random refactor direction
- lowers architecture drift risk
- resolves ambiguity before structural code movement begins

### Phase 2 exit gate

Do not start Phase 3 until:

- ownership is explicit
- access-control policy is explicit
- target shell model is explicit

## 6. Phase 3: Domain and Invariant Normalization

Objective:

Create canonical business language and define where invariants are enforced.

### P3-T1. Inventory all business-state literals

- Order: 1
- Type: analysis
- Dependencies: Phase 2 complete
- Impact: high
- Owner: backend engineer
- Artifact:
  - inventory of:
    - roles
    - incident statuses
    - incident categories
    - incident severities
    - checklist results
- Verification:
  - every current literal source is cataloged

### P3-T2. Define canonical domain types

- Order: 2
- Type: decision/design
- Dependencies: P3-T1
- Impact: critical
- Owner: senior engineer
- Artifact:
  - domain enum/value definitions
- Verification:
  - one canonical type exists for each critical business-state family

### P3-T3. Define invariant ownership map

- Order: 3
- Type: decision/design
- Dependencies: P3-T2
- Impact: critical
- Owner: senior engineer
- Artifact:
  - invariant map stating what belongs at:
    - schema
    - domain
    - application/use-case
    - presentation
- Verification:
  - examples are resolved, including:
    - exactly one active template assumption
    - incident status transition rules
    - checklist completion rules

### P3-T4. Define data transition and migration strategy

- Order: 4
- Type: migration planning
- Dependencies: P3-T2, P3-T3
- Impact: critical
- Owner: senior engineer / backend engineer
- Artifact:
  - data transition plan
- Verification:
  - any change to stored values or constraints has:
    - migration approach
    - backfill approach
    - rollback consideration

### P3-T5. Update tests and docs plan for canonical domain definitions

- Order: 5
- Type: testing/documentation planning
- Dependencies: P3-T2
- Impact: medium
- Owner: backend engineer
- Artifact:
  - domain normalization test plan
- Verification:
  - tests no longer depend on scattered unmanaged literals as the long-term target

### Phase 3 verification checklist

- business-state literals inventoried
- canonical types defined
- invariant ownership map defined
- migration/backfill strategy defined
- test update strategy defined

### Phase 3 impact summary

- lowers drift risk
- enables safe workflow extraction
- turns business vocabulary into enforceable code contracts

### Phase 3 exit gate

Do not start Phase 4 until:

- canonical domain definitions are ready
- invariant ownership is agreed
- data transition strategy exists

## 7. Phase 4: Application Workflow Extraction

Objective:

Extract workflow logic from presentation into application-layer use cases.

### P4-T1. Extract checklist daily-run initialization use case

- Order: 1
- Type: refactor
- Dependencies: Phase 3 complete
- Impact: critical
- Owner: backend engineer
- Artifact:
  - application-layer checklist initialization service/action
- Verification:
  - component no longer owns template resolution and run creation orchestration
  - existing behavior preserved

### P4-T2. Extract checklist submission use case

- Order: 2
- Type: refactor
- Dependencies: P4-T1
- Impact: critical
- Owner: backend engineer
- Artifact:
  - application-layer checklist submission service/action
- Verification:
  - component no longer performs direct submission persistence workflow
  - validation and persistence still behave correctly

### P4-T3. Extract incident creation use case

- Order: 3
- Type: refactor
- Dependencies: Phase 3 complete
- Impact: critical
- Owner: backend engineer
- Artifact:
  - application-layer incident creation service/action
- Verification:
  - file handling, incident creation, and activity creation are orchestrated outside the component

### P4-T4. Extract incident status transition use case

- Order: 4
- Type: refactor
- Dependencies: Phase 3 complete
- Impact: critical
- Owner: backend engineer
- Artifact:
  - application-layer incident transition service/action
- Verification:
  - transition rule, resolved timestamp policy, and activity logging are externalized from the component

### P4-T5. Extract dashboard aggregation/query use case

- Order: 5
- Type: refactor
- Dependencies: Phase 3 complete
- Impact: high
- Owner: backend engineer
- Artifact:
  - application-layer dashboard query/aggregation service
- Verification:
  - controller becomes thin
  - aggregation rules are testable outside the controller

### P4-T6. Add service-level tests for extracted workflows

- Order: 6
- Type: testing
- Dependencies: P4-T1 to P4-T5
- Impact: high
- Owner: backend engineer
- Artifact:
  - application/service tests
- Verification:
  - workflow logic is testable without Livewire UI orchestration

### P4-T7. Revalidate authorization enforcement after extraction

- Order: 7
- Type: validation
- Dependencies: P4-T1 to P4-T5, Phase 2 authorization policy
- Impact: high
- Owner: senior engineer
- Artifact:
  - authorization verification notes
- Verification:
  - extracted workflows still enforce intended access rules

### Phase 4 verification checklist

- core workflows no longer live primarily in UI components
- feature tests still pass
- service-level tests exist for extracted workflows
- authorization behavior still matches policy

### Phase 4 impact summary

- delivers the largest structural improvement
- makes future scaling and maintenance significantly easier

### Phase 4 exit gate

Do not start Phase 5 until:

- at least the four critical workflows are application-layer driven
- UI components are materially thinner
- workflow tests exist below the UI level

## 8. Phase 5: Shell and Presentation Consolidation

Objective:

Create one maintainable presentation shell and remove duplicated layout logic.

### P5-T1. Define final target shell implementation

- Order: 1
- Type: decision confirmation
- Dependencies: Phase 4 complete
- Impact: high
- Owner: frontend/full-stack engineer
- Artifact:
  - final shell implementation choice
- Verification:
  - no ambiguity remains around which file/path is the app shell authority

### P5-T2. Consolidate authenticated app shell

- Order: 2
- Type: refactor
- Dependencies: P5-T1
- Impact: critical
- Owner: frontend/full-stack engineer
- Artifact:
  - consolidated app shell
- Verification:
  - duplicated full-document app shells are removed or deprecated
  - desktop/mobile navigation remains functional

### P5-T3. Normalize role-aware navigation rendering

- Order: 3
- Type: refactor
- Dependencies: P5-T2
- Impact: high
- Owner: frontend/full-stack engineer
- Artifact:
  - reusable navigation definition or component strategy
- Verification:
  - role-based nav is not duplicated across multiple shells

### P5-T4. Review auth/settings shell strategy

- Order: 4
- Type: decision/refactor-planning
- Dependencies: P5-T2
- Impact: medium
- Owner: senior engineer
- Artifact:
  - decision note:
    - keep Volt temporarily
    - or migrate settings/auth into the unified shell strategy
- Verification:
  - temporary vs permanent status is explicit

### P5-T5. Run navigation and rendering regression verification

- Order: 5
- Type: validation
- Dependencies: P5-T2, P5-T3
- Impact: high
- Owner: QA-minded engineer / full-stack engineer
- Artifact:
  - regression notes
- Verification:
  - route navigation still works
  - role-specific nav still works
  - mobile and desktop render correctly

### Phase 5 verification checklist

- one authoritative app shell exists
- duplicated nav rendering is removed or minimized to one reusable strategy
- auth/settings shell decision is explicit
- no role-navigation regressions remain

### Phase 5 impact summary

- removes duplicated layout debt
- lowers future frontend maintenance cost

### Phase 5 exit gate

Do not start Phase 6 until:

- app shell is authoritative
- navigation regressions are cleared
- auth/settings shell direction is explicit

## 9. Phase 6: Repository Hygiene and Source-of-Truth Cleanup

Objective:

Make the repository reflect engineering intent and remove misleading/stale artifacts.

### P6-T1. Define tracked-artifact policy

- Order: 1
- Type: decision
- Dependencies: Phase 5 complete
- Impact: high
- Owner: senior engineer
- Artifact:
  - tracked-artifact policy
- Verification:
  - team can answer what belongs in git and why

### P6-T2. Audit currently tracked public/generated assets against policy

- Order: 2
- Type: analysis
- Dependencies: P6-T1
- Impact: high
- Owner: backend/full-stack engineer
- Artifact:
  - keep/remove list for tracked generated assets
- Verification:
  - every tracked public/generated artifact is classified

### P6-T3. Remove or replace non-source artifacts in a controlled pass

- Order: 3
- Type: cleanup/refactor
- Dependencies: P6-T2
- Impact: high
- Owner: full-stack engineer
- Artifact:
  - cleaned repository contents
- Verification:
  - app still boots from clean clone
  - required assets regenerate through documented process

### P6-T4. Normalize project metadata and scaffold residue

- Order: 4
- Type: cleanup
- Dependencies: P6-T3
- Impact: medium
- Owner: backend engineer
- Artifact:
  - updated metadata
  - removed misleading starter identity
- Verification:
  - package identity reflects the real project

### P6-T5. Review `⚡`-named files and residual mixed-paradigm artifacts

- Order: 5
- Type: cleanup/decision
- Dependencies: P6-T4
- Impact: medium
- Owner: senior engineer
- Artifact:
  - keep/rename/migrate decision for Volt-related files and residue
- Verification:
  - no ambiguous naming remains without intent

### P6-T6. Remove dead or misleading auth/account residue

- Order: 6
- Type: cleanup
- Dependencies: Phase 2 authorization policy, P6-T4
- Impact: medium
- Owner: backend engineer
- Artifact:
  - cleaned auth/account surface
- Verification:
  - unsupported flows are removed or explicitly documented

### P6-T7. Perform clean-clone verification

- Order: 7
- Type: validation
- Dependencies: P6-T3 to P6-T6
- Impact: critical
- Owner: QA-minded engineer / senior engineer
- Artifact:
  - clean-clone verification evidence
- Verification:
  - fresh clone can install, bootstrap, run tests, and run app per docs

### Phase 6 verification checklist

- tracked artifact policy exists
- generated artifacts are no longer ambiguous
- metadata reflects the real project
- misleading scaffold/auth residue is removed
- clean clone works end-to-end

### Phase 6 impact summary

- makes repository maintainable
- lowers onboarding cost
- reduces stale artifact risk

## 10. Parallelization Guidance

Parallel work is allowed only where it does not violate phase gates.

Safe examples:

- in Phase 1:
  - CI normalization and README normalization can overlap after baseline decision
- in Phase 3:
  - literal inventory and invariant inventory can overlap
- in Phase 4:
  - incident creation extraction and dashboard extraction may overlap if write scopes are clearly separated

Unsafe examples:

- starting workflow extraction before invariant ownership is clear
- cleaning tracked artifacts before build/source policy is decided
- consolidating shells before authorization and workflow boundaries are stable

## 11. Suggested Ownership Model

This is a role model, not a staffing requirement.

- Senior engineer / architect:
  - phase gates
  - architectural decisions
  - authorization policy
  - invariant ownership
- Backend engineer:
  - workflow extraction
  - domain normalization
  - schema/data transition work
- Full-stack engineer:
  - shell consolidation
  - navigation normalization
  - asset cleanup validation
- QA-minded engineer:
  - regression evidence
  - clean-clone validation
  - behavior verification

## 12. Readiness Assessment

Current readiness status, based on the completed audit and plan:

### What is already ready

- the codebase has been audited
- the remediation plan exists
- the major architectural choices are now explicit
- the main hidden gaps have been surfaced:
  - environment mismatch
  - runtime storage operability
  - authorization/account lifecycle ambiguity
  - data transition planning need

### What is still needed before true execution begins

- explicit confirmation that the team accepts:
  - PHP 8.4 minimum
  - Filament/custom Livewire split ownership
  - phased remediation instead of feature-first work
- a remediation branch/review policy
- agreement that feature work is frozen during foundation remediation

### Brutal readiness truth

You are not "ready to code immediately without coordination."

You are "ready to start systematic remediation" once the governance start conditions are confirmed.

That means:

- technically: almost ready
- operationally: ready after Phase 0 is explicitly activated

## 13. Exact Start Condition

You are ready to begin execution when all of the following are true:

1. This Execute Preparation Pack is accepted as the working backlog.
2. Phase 0 freeze is explicitly active.
3. A remediation branch/review workflow is chosen.
4. The team accepts the major plan decisions in Document 18.

If those four conditions are met, you can start with:

- Phase 0 immediately
- Phase 1 right after Phase 0 gate closes

## 14. First Action When Execution Starts

The first concrete action should be:

`P0-T1 Declare remediation freeze`

Why:

- it prevents contamination of the effort
- it turns the work from "good intentions" into a governed program
- it protects all later technical phases from churn

The second action should be:

`P1-T1 Lock the supported PHP baseline`

Why:

- CI truth is currently the most critical technical contradiction
- nothing else should proceed while the platform contract is false

## 15. Final Summary

This project is ready for systematic remediation, but not for chaotic remediation.

You are ready to begin once the governance start conditions are explicitly activated.

From that point onward, the correct starting sequence is:

1. Phase 0 governance freeze
2. Phase 1 platform/environment/build truth
3. continue strictly by phase gate

That is the point at which you can say, truthfully:

"We are ready to start fixing this codebase correctly, in order, and with engineering discipline."

