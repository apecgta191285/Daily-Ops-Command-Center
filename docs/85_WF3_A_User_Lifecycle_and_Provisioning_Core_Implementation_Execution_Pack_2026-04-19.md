# WF3-A User Lifecycle and Provisioning Core Implementation Execution Pack

**Date:** 2026-04-19  
**Parent Plan:** `83_WF3_User_Administration_Lite_Master_Plan_2026-04-19.md`  
**Planning Pack:** `84_WF3_A_User_Lifecycle_and_Provisioning_Core_Execution_Pack_2026-04-19.md`

---

## What Landed

WF3-A is now live in the application layer.

This round adds real admin-owned lifecycle owners for:

- internal user creation
- user profile/admin updates
- role changes
- active/inactive lifecycle changes
- explicit admin password set/reset during lifecycle management

This means the repository no longer depends on manual database edits to prove that internal account lifecycle is operable.

---

## Product Truth After WF3-A

- only admins may provision or update users through the new lifecycle owners
- valid roles remain `admin`, `supervisor`, and `staff`
- inactive users are still blocked by the existing authentication policy
- password handling stays lightweight and internal:
  - admin can set an explicit initial password
  - admin can explicitly reset a password during lifecycle updates

No invitation flow, email handoff, or RBAC expansion was introduced.

---

## Why This Is the Right Scope

WF3-A intentionally stops before the full `/users` UI.

That is deliberate.

If the repository had landed a user list before lifecycle actions existed, we would have created a decorative admin surface with no real product truth behind it.

This round prevents that.

---

## Files and Ownership

Primary owners added:

- `app/Application/Users/Actions/CreateManagedUser.php`
- `app/Application/Users/Actions/UpdateManagedUser.php`
- `app/Application/Users/Support/UserAdministrationValidator.php`

Regression proof added:

- `tests/Feature/Application/UserAdministrationActionsTest.php`

---

## Verification Expectations

WF3-A is considered landed only if:

- admin can create a user
- invalid role updates are rejected
- admin can activate/deactivate a user
- inactive user remains blocked by authentication policy after deactivation
- non-admin cannot use lifecycle owners

---

## What Comes Next

The next correct step is:

- `WF3-B User Administration Surface`

That is where the product should expose:

- user roster
- create form
- edit form
- active/inactive cues

But now it can do so honestly, because lifecycle truth exists underneath.
