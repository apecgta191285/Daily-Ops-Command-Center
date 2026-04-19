# WF3-C Account Safety and Access Guard Rails Execution Pack

**Date:** 2026-04-19  
**Parent Plan:** `83_WF3_User_Administration_Lite_Master_Plan_2026-04-19.md`  
**Execution Standard:** No hidden lifecycle traps, no Blade-owned safety rules, no fake “admin can do anything” shortcuts

---

## 1. Why WF3-C Exists

WF3-A and WF3-B already made user lifecycle real:

- admins can provision accounts
- admins can edit role, active state, and password
- the product now exposes a real user-administration surface

That made one risk visible immediately:

- an admin could attempt to deactivate or demote the wrong administrator account
- an admin could potentially try to lock themselves out from inside the surface

WF3-C exists to harden the lifecycle now that the workflow is real.

---

## 2. Goal

Prevent account-lifecycle actions from becoming self-destructive while keeping the workflow lightweight and honest.

This round should make the product safer without expanding into enterprise IAM or overbuilt approval logic.

---

## 3. Scope

WF3-C introduces:

- application-owned guard rails for administrator lifecycle changes
- explicit rejection of self-deactivation
- explicit rejection of self-demotion out of the admin lane
- protection against removing the final active administrator from the system
- clearer copy on the admin surface so reset and access-state meaning stay predictable

---

## 4. Product Rules Locked

1. At least one active administrator must remain in the system.
2. Administrators cannot deactivate their own account through the admin lifecycle workflow.
3. Administrators cannot remove their own admin role through the admin lifecycle workflow.
4. Password change remains explicit and internal; it is a direct set/reset path, not an invitation or email workflow.

---

## 5. Implementation Notes

- Guard rails belong in the application layer so Livewire and Blade stay thin.
- UI should reflect the real guard rails instead of offering false affordances.
- Validation failures should return field-level errors for `role` and/or `is_active` rather than crashing with generic authorization failure.

---

## 6. Required Regression Proof

- admin cannot deactivate their own account
- admin cannot demote their own account out of the admin role
- last active administrator cannot be removed from active admin coverage
- another admin can still be deactivated when coverage remains
- user administration surface shows honest copy for self-edit guard rails

---

## 7. Completion Standard

WF3-C is complete only when:

1. the product cannot casually lock itself out of active admin coverage
2. lifecycle guard rails are application-owned
3. the UI reflects those constraints honestly
4. password/reset copy stays aligned with the lightweight internal model

---

## 8. What Comes Next

After WF3-C lands correctly, the next step is:

- `WF3-D Quality Hardening and Documentation`

That is the round where WF3 should be closed with canonical documentation and regression proof aligned to the final lifecycle contract.
