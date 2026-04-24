# Program 2 / Priority 6 - Authorization Depth Hardening

Date: 2026-04-24

## Why this round happened

Route-level role gates were still doing most of the authorization work for management read and update surfaces. That was directionally acceptable for the earlier product scope, but it left incident detail, incident evidence download, incident print, checklist history recap, and checklist print surfaces without explicit object-level authorization owners.

This round tightened authorization depth without opening a broad policy wave.

## Scope

- Add explicit policies for `Incident` and `ChecklistRun`
- Register policy mappings in the application provider
- Enforce policy checks inside management controllers and Livewire components that operate on concrete incident/checklist resources
- Add focused regression proof that Gate registration and policy outcomes match the intended role model

## Files changed

- `app/Policies/IncidentPolicy.php`
- `app/Policies/ChecklistRunPolicy.php`
- `app/Providers/AppServiceProvider.php`
- `app/Http/Controllers/Management/DownloadIncidentAttachmentController.php`
- `app/Http/Controllers/Management/PrintIncidentSummaryController.php`
- `app/Http/Controllers/Management/PrintChecklistRunRecapController.php`
- `app/Livewire/Management/Incidents/Show.php`
- `app/Livewire/Management/Checklists/HistoryShow.php`
- `tests/Feature/AuthorizationDepthTest.php`

## What tightened

- Management incident surfaces now require object-level `IncidentPolicy` authorization in addition to route role gates.
- Historical checklist recap and printable recap now require object-level `ChecklistRunPolicy` authorization after preserving the existing `404` behavior for unsubmitted runs.
- Incident status and accountability mutations now authorize against the specific incident before calling the application actions.

## What stayed intentionally unchanged

- No schema or migration changes
- No broad policy rollout across every resource in the product
- No route rewrite
- No behavior change to the existing `404` semantics for unsubmitted checklist runs

## Residual debt after this round

- Authorization is deeper on the most important management resources, but the repo still does not have a fully policy-driven authorization model across every future admin/management surface.
- This round was intentionally narrow and did not open a full authorization architecture wave.
