# WF2-A Incident Accountability Core Execution Pack

**Date:** 2026-04-19  
**Status:** Implemented  
**Parent Plan:** `77_WF2_Incident_Ownership_Lite_Master_Plan_2026-04-19.md`
**Execution Standard:** Lightweight accountability, no fake enterprise workflow

---

## 1. Outcome

WF2-A is now live in the codebase.

Incidents can now persist:

- an optional management owner
- an optional follow-up target date

without turning incident management into a notification, escalation, or SLA system.

---

## 2. What Landed

### Data contract

- `incidents.owner_id` added as nullable foreign key to `users.id`
- `incidents.follow_up_due_at` added as nullable date field

### Application ownership

- status transitions remain owned by `TransitionIncidentStatus`
- accountability changes now have their own owner in `UpdateIncidentAccountability`

This keeps the product language clear:

- status lane = queue movement
- accountability lane = responsibility and next review target

### Management surface

Incident detail now includes:

- owner selection limited to management-capable users
- follow-up target date input
- accountability snapshot in the hero and reference lane
- timeline entries for ownership and follow-up target changes

---

## 3. Architectural Decisions

### Chosen path

`WF2-A` intentionally used **Path B** from the planning pack:

- separate accountability action
- no overloading of the existing status transition owner

### Why

If owner and follow-up changes had been added into `TransitionIncidentStatus`, that action would have started drifting into a general-purpose incident mutation owner.

That would have made later queue and dashboard work harder to reason about.

---

## 4. Behavior Rules

1. Incident owner is optional.
2. Incident owner must be `admin` or `supervisor`.
3. Follow-up target date is optional.
4. Follow-up target date is an operational target, not an SLA clock.
5. Status-only updates still work when accountability fields are untouched.
6. Accountability-only updates do not require a status change.

---

## 5. Verification

Passed after implementation:

- `composer lint:check`
- `php artisan test`
- `npm run build`
- `composer test:browser`

Browser smoke now confirms that the incident detail surface includes:

- `Accountability lane`
- owner selector
- follow-up target date input

---

## 6. Next Correct Step

The next valuable slice is:

`WF2-B Queue and Detail Surface Upgrade`

That wave should build on the new truth already in the model and incident detail surface, and add:

- unowned / mine / overdue queue framing
- stronger accountability visibility in incident list surfaces
- clearer management follow-up language in the queue

It should **not** jump into notifications, escalations, or reassignment history.
