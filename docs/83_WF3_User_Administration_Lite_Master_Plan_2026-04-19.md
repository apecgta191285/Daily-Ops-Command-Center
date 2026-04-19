# WF3 User Administration Lite Master Plan

**Date:** 2026-04-19  
**Status:** Approved planning baseline  
**Execution Standard:** No Quick & Dirty, no pseudo-RBAC expansion, no hidden account lifecycle rules

---

## 1. Why WF3 Exists

The repository already contains real account lifecycle truth:

- users have canonical roles
- users have active/inactive state
- inactive users are blocked at authentication time
- protected routes enforce active account state
- login redirects are already role-aware

But that truth is still mostly hidden inside implementation.

Right now the product can answer:

- what roles exist
- whether an inactive account should be blocked

But the product still cannot answer operationally:

- who can create a new user
- how an admin changes role safely
- how an admin deactivates access without touching the database manually
- how account lifecycle is operated from inside the product

That is why the system still feels partly demo-only even though the underlying lifecycle contract already exists.

WF3 exists to expose that lifecycle as a real admin capability.

---

## 2. WF3 Product Goal

Turn account lifecycle from hidden code policy into a small, believable admin workflow.

After WF3:

- admins can see the current user roster
- admins can create an internal user account
- admins can change role safely
- admins can activate/deactivate accounts safely
- admins can hand off or trigger a controlled password-reset path without inventing a helpdesk system

The goal is not enterprise IAM.

The goal is:

> enough user administration to make the app operable by a small real team

---

## 3. What WF3 Is Not

WF3 will **not** introduce:

- organizations or multi-tenant workspace management
- team hierarchy modeling
- granular permission matrix
- audit-heavy IAM platform behavior
- SSO / SCIM / external directory sync
- invitation pipeline with email delivery dependency
- approval workflow for role changes

The target is deliberately small:

> one internal user roster, one controlled create/edit path, one clear active/inactive lifecycle

---

## 4. Current Truth Before WF3

The current repository already has:

- `role`
- `is_active`
- active-user authentication enforcement
- role-based route gates
- settings/account security surfaces for the currently signed-in user

The current repository does **not** yet have:

- admin-owned user list
- create user surface
- role management surface
- active/inactive lifecycle surface
- password reset handoff owned by admins

That means WF3 is not building raw lifecycle rules from nothing.

WF3 is surfacing and governing rules that already exist.

---

## 5. Target Product Model

### Current model

- accounts are provisioned internally
- public registration is unsupported
- access depends on role and `is_active`
- lifecycle changes are not product-owned yet

### Target model

- admin has a dedicated user administration surface
- each account has:
  - name
  - email
  - role
  - active/inactive state
- account lifecycle stays lightweight and internal
- password management remains controlled and simple

### Product rules

1. User administration remains admin-only.
2. Supervisor does not become a user-admin role in WF3.
3. Active/inactive remains the canonical access gate.
4. Role changes remain coarse:
   - admin
   - supervisor
   - staff
5. WF3 must not reopen public sign-up.

---

## 6. Phase Map

### WF3-A User Lifecycle and Provisioning Core

**Goal**

Make account administration real in persistence and application flow.

**Scope**

- define the minimum create/update account action(s)
- support admin creation of internal users
- support safe updates to:
  - name
  - email
  - role
  - active state
- define the controlled password-reset handoff rule

**Success criteria**

- admins can provision a user without touching seed data or the database manually
- invalid role/state updates are rejected safely
- account lifecycle logic has one clear owner in the application layer

---

### WF3-B User Administration Surface

**Goal**

Make lifecycle operable inside the app shell.

**Scope**

- admin user list surface
- create/edit flow
- visible role and active/inactive cues
- clear copy around what active/inactive means

**Success criteria**

- admin can scan current roster quickly
- admin can create and update users inside the product
- inactive accounts read as intentionally disabled, not broken

---

### WF3-C Account Safety and Access Guard Rails

**Goal**

Prevent lifecycle actions from becoming self-destructive or confusing.

**Scope**

- protect against accidental self-deactivation
- protect against invalid role demotion edge cases
- clarify reset-password handoff
- add targeted regression around authorization and account-state changes

**Success criteria**

- admins cannot casually lock the system into an unusable state
- lifecycle copy is honest and predictable
- access rules stay aligned with Fortify and middleware behavior

---

### WF3-D Quality Hardening and Documentation

**Goal**

Close the wave with regression proof and canonical docs alignment.

**Scope**

- feature/unit/browser coverage
- README/current state updates
- decision/system/data/architecture docs update if contract changes

**Success criteria**

- user lifecycle truth is fully documented
- tests prove the admin lifecycle workflow end to end

---

## 7. Recommended Execution Order

1. **WF3-A User Lifecycle and Provisioning Core**
2. **WF3-B User Administration Surface**
3. **WF3-C Account Safety and Access Guard Rails**
4. **WF3-D Quality Hardening and Documentation**

### Why this order

- lifecycle truth must exist before the UI can operate it
- the admin surface must sit on real application actions
- safety rules should harden the real UX, not block planning progress
- docs should close the wave after the contract is real

---

## 8. Engineering Principles for WF3

1. Do not introduce fine-grained RBAC just because user management exists.
2. Keep lifecycle logic in application owners, not in Blade or Livewire conditionals.
3. Treat `is_active` as the real access switch; do not invent shadow states.
4. Keep user creation internal and explicit; no public sign-up backdoor.
5. Reuse the current app shell and admin language; do not create a second internal tool aesthetic.

---

## 9. Key Risks

### Risk 1: Overbuilding administration

If WF3 turns into organization management, the product will leave A-lite scope quickly.

### Risk 2: UI without lifecycle truth

If a user list lands before create/update actions and rules are clean, the product will gain a fake admin surface.

### Risk 3: Self-lockout and admin drift

If lifecycle changes ignore guard rails, admins can deactivate or demote the wrong account and create avoidable recovery pain.

---

## 10. Expected Product Effect

After WF3 completes, the product should feel like:

- checklist runtime is real
- incidents are accountable
- users can actually be operated inside the app

That is the smallest meaningful step from:

- “this system can be demoed”

to:

- “this system can be run by a small internal team”
