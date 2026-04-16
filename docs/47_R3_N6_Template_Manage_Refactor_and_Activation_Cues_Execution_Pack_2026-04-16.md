# R3 + N6 Template Manage Refactor and Activation Cues Execution Pack

Date: 2026-04-16
Status: Executed

## Objective

Make checklist template administration safer and easier to evolve by reducing `Manage.php` responsibility while also surfacing the real activation impact before an admin saves a template.

## Why This Slice

After template duplication and lightweight grouping, the next risk was not missing CRUD capability. The risk was that the template manage surface would grow into a God-form before the admin workflow became more informative. This slice addresses both issues together:

- extract item-editor concerns out of the Livewire component
- add activation-impact guidance so admins understand what happens when a template becomes live

## Scope

- Extract checklist item editor add/remove/hydration behavior into a dedicated support class
- Extract activation-impact messaging into a dedicated support class
- Show activation impact directly in the template manage screen
- Show current live template context when relevant
- Add regression coverage for activation guidance and template editor helpers

## Decisions

- Keep the template manage surface inside the main app shell
- Do not add full template versioning or approval workflows
- Keep activation behavior unchanged; improve guidance, not business rules
- Keep the refactor lightweight and avoid turning template management into a mini-builder product

## Acceptance Criteria

- `Manage.php` no longer owns low-level item add/remove/hydration logic directly
- Admins can see what activating a template will do before saving
- Create and edit flows show live-template context when it matters
- Template behavior remains unchanged for duplicate, save, and activation flows
- Feature and unit tests cover the new helper classes and activation guidance

## Verification

- `composer lint:check`
- `php artisan test`
- `composer test:browser`

## Outcome

Template administration is now safer in two ways:

- the code is easier to extend without overloading the Livewire component
- the UI explains live-template impact before an admin changes production behavior
