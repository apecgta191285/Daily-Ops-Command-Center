# Remediation Governance Protocol

Date: 2026-04-11
Project: Daily Ops Command Center
Execution phase coverage:

- Phase 0: Governance Freeze
- Phase 1 prerequisite governance confirmation

Reference inputs:

- [17_Codebase_Audit_Report_2026-04-10.md](./17_Codebase_Audit_Report_2026-04-10.md)
- [18_Foundation_Remediation_Plan_2026-04-11.md](./18_Foundation_Remediation_Plan_2026-04-11.md)
- [19_Execute_Preparation_Pack_2026-04-11.md](./19_Execute_Preparation_Pack_2026-04-11.md)
- [20_Pre_Execute_Readiness_Alignment_2026-04-11.md](./20_Pre_Execute_Readiness_Alignment_2026-04-11.md)

## 1. Purpose

This document is the operational governance artifact for foundation remediation.

Its purpose is to:

- stop entropy while remediation is in progress
- prevent feature pressure from distorting architecture work
- define how remediation changes are reviewed
- define what evidence is required before a phase can be called complete

## 2. Remediation Freeze Declaration

Foundation remediation freeze status: `ACTIVE`

Effective date: 2026-04-11

Scope of the freeze:

- no new product features are to be introduced during remediation phases
- no opportunistic UI redesign unrelated to the active remediation task
- no dependency additions unless they remove more complexity than they introduce and are justified in the active phase artifact
- no schema changes outside the approved phase backlog
- no direct commits to `main` for remediation work without review evidence

Allowed work during freeze:

- changes explicitly tied to the active phase backlog
- tests needed to prove behavioral preservation
- documentation required to make architectural and operational contracts explicit
- cleanup that is required to preserve one source of truth

Disallowed work during freeze:

- adding unrelated modules
- starting side refactors because a file is already open
- mixing feature delivery into remediation commits
- expanding scope inside the same branch without updating the execution artifact

## 3. Branch and Review Policy

Canonical branch strategy for remediation:

- branch prefix: `remediation/`
- branch naming pattern:
  - `remediation/p1-platform-truth`
  - `remediation/p2-boundary-definition`
  - `remediation/p3-domain-normalization`

Review expectations:

- every remediation branch must be scoped to one phase or one explicitly linked subtask
- every remediation change must include updated verification evidence
- architectural decisions must be captured in docs before or alongside structural code movement
- high-impact refactors require behavior-preserving tests in the same branch

Merge policy:

- no direct remediation commits to `main`
- merge only after:
  - lint passes
  - relevant tests pass
  - phase artifact is updated
  - change scope matches the declared branch purpose

## 4. Evidence Standard

Every remediation phase must leave the repository with evidence in all applicable categories:

### A. Documentation evidence

- updated phase artifact or execution report
- updated architectural or operational decision record when the phase changes a contract
- updated README or setup documentation if bootstrap, CI, or runtime expectations changed

### B. Verification evidence

- exact commands executed
- exact pass/fail outcomes
- statement of what was not verified and why

### C. Regression evidence

- tests added or updated to guard the changed contract
- explicit note when behavior was preserved intentionally
- explicit note when behavior changed intentionally

### D. Scope-control evidence

- statement of what the phase intentionally did not change
- statement of what remains for the next phase

## 5. Phase Completion Rule

A phase is complete only when:

- its artifact exists
- its verification checklist is satisfied as far as can be proven locally
- no unresolved contradiction remains inside the scope of that phase
- the next phase can start without re-deciding the same contract

## 6. Current Governance Assessment

As of 2026-04-11:

- remediation freeze is declared active
- branch/review policy is now explicit in this document
- evidence standard is now explicit in this document

Conclusion:

Phase 0 governance requirements are satisfied at the repository-documentation level.
