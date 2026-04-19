# WF3-A User Lifecycle and Provisioning Core Execution Pack

**Date:** 2026-04-19  
**Parent Plan:** `83_WF3_User_Administration_Lite_Master_Plan_2026-04-19.md`  
**Execution Standard:** No Quick & Dirty, no fake admin UI, no hidden lifecycle side effects

---

## 1. Why WF3-A Exists

The repository already has account lifecycle truth in code:

- users have role
- users have `is_active`
- authentication rejects inactive users
- protected routes enforce active state

But there is still no admin-owned application action for account lifecycle.

If we jump straight to a user list page first, we will create a decorative admin surface that still depends on manual database edits.

WF3-A exists to prevent that mistake.

---

## 2. Goal

Land the minimum persistence and application truth required for real internal user administration.

This round should make it possible for later WF3-B surfaces to operate honestly.

---

## 3. Scope

WF3-A should introduce:

- application owner(s) for account creation and account updates
- controlled role updates
- controlled active/inactive updates
- minimum password-initialization or reset-handoff strategy
- validation and authorization boundaries for admin-only lifecycle operations

This round may touch:

- user create/update actions
- user lifecycle DTO/result objects if they improve ownership clarity
- route contract for future admin user management family
- regression tests around lifecycle invariants

---

## 4. Product Rules Locked for Implementation

1. Only admins can provision or change users in WF3.
2. Valid roles remain:
   - `admin`
   - `supervisor`
   - `staff`
3. `is_active = false` means the account cannot authenticate or continue using protected routes.
4. Public self-registration remains unsupported.
5. WF3-A must not invent invitation mail infrastructure.
6. Password handling must remain simple and internal:
   - either admin sets an initial password explicitly
   - or admin triggers a controlled reset path already supported by the product

---

## 5. Strong Recommendation on Password Strategy

For A-lite scope, the most practical strategy is:

- admin creates user with an explicit initial password
- the user can later change it through existing account/security flows

Why this is preferred now:

- no email-delivery dependency
- no invitation infrastructure
- no half-real reset orchestration
- deterministic for local demo and internal operation

What WF3-A should avoid:

- adding invitation tokens
- pretending there is a helpdesk workflow
- building email-first lifecycle without guaranteed delivery infrastructure

---

## 6. Proposed Ownership Shape

Recommended application owners:

- one create action for internal user provisioning
- one update action for lifecycle/profile/admin fields

The exact file count is secondary.

What matters is:

- role and active-state rules are not scattered
- password setup/update logic is not hidden inside Livewire
- future user admin surfaces can call one clear owner

---

## 7. Suggested Future Surface Contract

WF3-A should prepare for this route family in WF3-B:

- `/users`
- `/users/create`
- `/users/{user}/edit`

All should remain admin-only and inside the current shared application shell.

This execution pack does **not** require landing the full UI yet, but the domain/application truth should be shaped to support it cleanly.

---

## 8. Tests Required

WF3-A is not done without regression proof.

Required minimum coverage:

- feature test: admin can create a user
- feature test: invalid role is rejected
- feature test: admin can activate/deactivate a user
- feature test: inactive user still cannot authenticate after admin change
- feature test: non-admin cannot use lifecycle actions
- browser/smoke extension only if a minimal UI lands in the same round

---

## 9. Completion Standard

WF3-A is complete only when:

1. user lifecycle has a real application owner
2. admin-owned provisioning is possible without manual DB edits
3. active/inactive and role changes are validated safely
4. the current authentication lifecycle remains aligned with the new admin truth
5. later WF3-B UI work can build on real state, not pseudo-admin markup

---

## 10. What Comes Next

After WF3-A lands correctly, the next step is:

- `WF3-B User Administration Surface`

That is the point where the product should expose:

- roster
- create/edit flow
- lifecycle cues

But not before WF3-A makes the behavior real.
