# Pre-Execute Readiness Alignment

Date: 2026-04-11
Project: Daily Ops Command Center
Purpose: Record the readiness-blocker fixes completed before foundation remediation execution begins.

## Scope

This document captures the targeted corrections made to remove ambiguity that previously blocked a safe start to Execute.

This was not a feature phase.
This was not broad refactoring.
This was a readiness-alignment pass focused on:

- platform truth
- local bootstrap truth
- attachment/runtime operability
- account lifecycle enforcement
- documentation truthfulness

## Corrections Completed

### 1. Platform contract aligned

- `composer.json` now declares PHP `^8.4`
- `.github/workflows/tests.yml` now tests only PHP `8.4`
- Composer lock metadata was refreshed with `composer update --lock --no-install`

Result:

- declared platform support now matches the actual dependency graph and current runtime

### 2. Local bootstrap contract aligned

- `.env.example` now defaults to SQLite
- `.env.example` now uses `APP_URL=http://localhost:8000`
- `composer.json` setup script now creates `database/database.sqlite`
- `README.md` local setup now explicitly includes `php artisan storage:link`

Result:

- local setup instructions, environment template, and runtime profile no longer contradict one another

### 3. Attachment operability aligned

- local `public/storage` symlink was created with `php artisan storage:link`
- `README.md` now documents the attachment delivery prerequisite explicitly

Result:

- attachment URLs in local development now have the required runtime linkage

### 4. Account lifecycle policy made explicit in code

- inactive users are now blocked during authentication
- inactive users are logged out if they attempt to access protected routes
- Filament panel access now requires both management role and active account state
- protected app routes now require both `auth` and `active`

Result:

- `is_active` is no longer a passive database field

### 5. Public registration ambiguity removed

- Fortify registration action binding was removed
- Fortify registration view binding was removed
- registration UI residue was removed from the login screen
- unused registration artifacts were removed
- `README.md` now states that public self-registration is intentionally unsupported

Result:

- the repository now reflects the actual product policy instead of leaving dead public-registration residue behind

### 6. Test data contract aligned

- `database/factories/UserFactory.php` now defaults users to:
  - `role = staff`
  - `is_active = true`
- new authentication policy regression tests were added

Result:

- factories now reflect expected baseline user state
- policy enforcement is covered by tests

### 7. Historical documentation ambiguity reduced

- `docs/05_Decision_Log_v1.3.md` now carries an explicit historical note pointing readers to:
  - `18_Foundation_Remediation_Plan_2026-04-11.md`
  - `19_Execute_Preparation_Pack_2026-04-11.md`

Result:

- older baseline assumptions are less likely to be mistaken for current canonical guidance

## Verification Evidence

Commands executed successfully:

```bash
composer update --lock --no-install
php artisan storage:link
composer lint:check
php artisan test
php artisan about
php artisan route:list | rg "register|login|password|verify|logout|two-factor|settings|checklists/runs/today"
```

Observed outcomes:

- `composer lint:check` passed
- `php artisan test` passed with `36 tests` and `198 assertions`
- `php artisan about` reported:
  - PHP `8.4.19`
  - Database `sqlite`
  - URL `localhost:8000`
  - `public/storage` `LINKED`
- route list confirms:
  - login/password/verification/two-factor routes exist as expected
  - no public registration route is present

## Readiness Decision

Readiness status: `READY TO START EXECUTE`

Interpretation:

- the pre-execution blockers identified in the audit and planning review have been corrected to a level that supports structured remediation work
- the repository is now fit to enter Execute under the sequencing defined in:
  - `18_Foundation_Remediation_Plan_2026-04-11.md`
  - `19_Execute_Preparation_Pack_2026-04-11.md`

## Remaining Truths

The following items are still valid remediation work, but they are no longer blockers to starting Execute:

- architectural boundary consolidation
- shell ownership consolidation
- business-logic extraction from UI components
- domain constant centralization
- generated asset policy cleanup
- broader documentation rationalization

## Recommended Next Step

Begin Execute using `19_Execute_Preparation_Pack_2026-04-11.md`, starting from the first remaining task that has not already been completed by this readiness-alignment pass.
