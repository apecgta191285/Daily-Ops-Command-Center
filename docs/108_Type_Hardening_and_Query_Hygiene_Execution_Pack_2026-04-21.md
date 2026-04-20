# **Type Hardening and Query Hygiene**

**DOC-108 | Execution Pack**  
**Date:** 2026-04-21  
**Status:** Implemented

---

## **1. Intent**

Close the next hardening slice without opening any new product capability:

* add stronger enum-backed model typing where the repo already has stable domain enums
* reduce stringly-typed comparisons in core runtime paths
* paginate the management incident queue instead of rendering an unbounded collection
* keep route contracts, product scope, and UI behavior intact

---

## **2. Scope**

### **Type hardening**

* `app/Models/User.php`
* `app/Models/ChecklistTemplate.php`
* `app/Models/Incident.php`
* supporting read paths and policy helpers that consume these model attributes

### **Query hygiene**

* `app/Application/Incidents/Queries/ListIncidents.php`
* `app/Livewire/Management/Incidents/Index.php`
* `resources/views/livewire/management/incidents/index.blade.php`

### **Regression proof**

* incident management tests
* user administration tests
* incident creation tests
* transition action tests
* scenario helpers and one queue pagination proof

---

## **3. What Changed**

### **3.1 Enum-backed model casts landed**

The following model attributes now use enum casts:

* `User.role` → `UserRole`
* `ChecklistTemplate.scope` → `ChecklistScope`
* `Incident.status` → `IncidentStatus`
* `Incident.severity` → `IncidentSeverity`
* `Incident.category` → `IncidentCategory`

This removes a chunk of stringly-typed state from the core model layer and makes downstream comparisons clearer and safer.

### **3.2 Read-side consumers were updated to match**

Supporting code was adjusted so enum-backed values remain safe and readable across:

* middleware role checks
* user roster summary assembly
* incident status/follow-up policy checks
* incident detail and print surfaces
* checklist scope board and daily run initialization
* badges and display components that now normalize enum or string input safely

### **3.3 Incident queue now paginates**

`ListIncidents` now exposes:

* a reusable query builder
* collection mode for existing application tests
* paginated mode for the management queue surface

The management incident list now paginates instead of rendering the full filtered queue in one collection-heavy response.

### **3.4 No scope drift**

This round does **not**:

* introduce new routes
* change business rules
* alter authorization boundaries
* add reporting/export behavior
* reopen feature-wave work

It is a hardening round only.

---

## **4. Acceptance Result**

This round is successful when:

* core typed model attributes stop depending on raw string comparisons in key paths
* the incident queue remains filterable but no longer loads as one unbounded collection
* existing product behavior stays intact
* regression coverage proves the queue, incidents, and user governance surfaces still work

