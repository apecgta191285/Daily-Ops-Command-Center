# WF3-B User Administration Surface Execution Pack

**Date:** 2026-04-19  
**Parent Plan:** `83_WF3_User_Administration_Lite_Master_Plan_2026-04-19.md`  
**Execution Standard:** No fake admin UI, no lifecycle rules hidden in Livewire, no public-registration drift

---

## 1. Why WF3-B Exists

WF3-A already landed the application truth for:

- internal user provisioning
- role updates
- active/inactive lifecycle updates
- explicit password set/reset by admins

But without a real in-app surface, that truth still remains too hidden to feel like a product capability.

WF3-B exists to expose the lifecycle honestly inside the main application shell.

---

## 2. Goal

Land an admin-only user administration surface that is:

- visually part of the same premium app shell
- operationally honest about role and active-state meaning
- wired directly to the application actions introduced in WF3-A

This round should make the product capable of handling internal user lifecycle without manual database edits.

---

## 3. Scope

WF3-B introduces:

- `/users`
- `/users/create`
- `/users/{user}/edit`
- admin-only navigation entry for user administration
- roster surface with active/inactive cues
- create/edit lifecycle form inside the existing admin language
- role-governance context so lifecycle choices read operationally, not as abstract form fields

This round deliberately does **not** introduce:

- invitations
- email delivery
- approval flow
- granular permissions
- organization management

---

## 4. Product Rules Kept Intact

1. User administration remains admin-only.
2. `is_active` remains the canonical access switch.
3. Role set remains:
   - `admin`
   - `supervisor`
   - `staff`
4. Password setup/reset remains explicit and internal.
5. Public registration remains unsupported.

---

## 5. Implementation Notes

- Navigation extends the existing admin shell instead of creating a second internal-tool frame.
- Livewire components call `CreateManagedUser` and `UpdateManagedUser` directly.
- Validation ownership stays inside WF3-A application owners.
- The roster reads as governance, not just a flat database table.
- The create/edit surface explains the meaning of `role` and `is_active` in product language.

---

## 6. Required Regression Proof

- admin can access `/users`
- non-admin cannot access user-administration routes
- admin can create a user through the Livewire surface
- admin can update an existing user through the Livewire surface
- admin navigation now includes `Users`
- browser smoke covers roster and create page without JS/console regressions

---

## 7. Completion Standard

WF3-B is complete only when:

1. the product exposes a believable user roster
2. create/edit lifecycle work happens through the real application owners from WF3-A
3. active/inactive meaning is explicit on the surface
4. the shell/navigation contract treats user administration as a first-class admin lane

---

## 8. What Comes Next

After WF3-B lands correctly, the next step is:

- `WF3-C Account Safety and Access Guard Rails`

That is the round where self-deactivation and other risky lifecycle edge cases should be hardened more deliberately.
