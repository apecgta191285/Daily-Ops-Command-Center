<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function stabilizeDesktopVisualState($page)
{
    $page->script(<<<'JS'
        if (! document.getElementById('desktop-qa-disable-motion')) {
            const style = document.createElement('style');
            style.id = 'desktop-qa-disable-motion';
            style.textContent = `
                *,
                *::before,
                *::after {
                    animation: none !important;
                    transition: none !important;
                }

                [data-motion],
                [data-motion] * {
                    opacity: 1 !important;
                    transform: none !important;
                    filter: none !important;
                }
            `;
            document.head.appendChild(style);
        }

        document.querySelectorAll('[data-motion]').forEach((element) => {
            element.classList.add('is-visible');
            element.style.opacity = '1';
            element.style.transform = 'none';
            element.style.filter = 'none';
        });

        window.scrollTo(0, 0);
    JS);

    return $page->wait(0.25);
}

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-04-20 09:00:00'));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('guest desktop entry surfaces keep the product shell usable', function () {
    $homePage = visit('/')->on()->desktop();
    $loginPage = visit('/login')->on()->desktop();

    stabilizeDesktopVisualState($homePage)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertSee('Daily Ops Command Center')
        ->assertPresent('a[href="#main-content"]');

    stabilizeDesktopVisualState($loginPage)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertPresent('input[name="email"]')
        ->assertPresent('input[name="password"]')
        ->assertPresent('[data-test="login-button"]');
});

test('authenticated desktop shell exposes stable role navigation and active content', function () {
    $admin = $this->createUserForRole(UserRole::Admin, ['name' => 'Desktop QA Admin']);

    $this->createTemplateWithItems([
        'title' => 'Desktop QA opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $page = visit('/login')->on()->desktop();

    $page->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard');

    stabilizeDesktopVisualState($page)
        ->assertNoSmoke()
        ->assertPresent('body > [data-flux-sidebar]')
        ->assertPresent('body > [data-flux-main]')
        ->assertPresent('[data-flux-sidebar-item][data-current]')
        ->assertPresent('.ops-page__header')
        ->assertPresent('.ops-screen--dashboard');
});

test('desktop workflow surfaces keep forms queues and runtime boards reachable', function () {
    $admin = $this->createUserForRole(UserRole::Admin, ['name' => 'Desktop QA Workflow Admin']);
    $this->createRoom(['name' => 'Desktop QA Lab', 'code' => 'LAB-DQA']);

    $this->createTemplateWithItems([
        'title' => 'Desktop QA checklist template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $this->createIncidentWithActivity($admin, [
        'title' => 'Desktop QA unresolved incident',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
    ]);

    visit('/login')->on()->desktop()
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoSmoke();

    stabilizeDesktopVisualState(visit('/incidents')->on()->desktop())
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertPresent('.ops-screen--incident-queue')
        ->assertPresent('select[wire\\:model\\.live="status"]')
        ->assertPresent('.ops-table');

    stabilizeDesktopVisualState(visit('/incidents/history')->on()->desktop())
        ->assertNoSmoke()
        ->assertPresent('.ops-screen--incident-history')
        ->assertPresent('.ops-history-summary-grid');

    stabilizeDesktopVisualState(visit('/checklists/history')->on()->desktop())
        ->assertNoSmoke()
        ->assertPresent('.ops-screen--checklist-history')
        ->assertPresent('.ops-table, .ops-empty, .ops-history-summary-grid');

    stabilizeDesktopVisualState(visit('/templates')->on()->desktop())
        ->assertNoSmoke()
        ->assertPresent('.ops-screen--template-index')
        ->assertPresent('.ops-governance-grid')
        ->assertPresent('.ops-table');

    stabilizeDesktopVisualState(visit('/templates/create')->on()->desktop())
        ->assertNoSmoke()
        ->assertPresent('.ops-screen--template-authoring')
        ->assertPresent('input#title')
        ->assertPresent('textarea#description')
        ->assertPresent('.ops-command-grid--template');

    stabilizeDesktopVisualState(visit('/users')->on()->desktop())
        ->assertNoSmoke()
        ->assertPresent('.ops-screen--user-index')
        ->assertPresent('.ops-table');

    stabilizeDesktopVisualState(visit('/users/create')->on()->desktop())
        ->assertNoSmoke()
        ->assertPresent('.ops-screen--user-manage')
        ->assertPresent('input#name')
        ->assertPresent('select#role');
});

test('staff desktop checklist runtime remains task first', function () {
    $staff = $this->createUserForRole(UserRole::Staff, ['name' => 'Desktop QA Staff']);
    $this->createRoom(['name' => 'Desktop QA Lab', 'code' => 'LAB-DQA']);

    $this->createTemplateWithItems([
        'title' => 'Desktop QA checklist template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    visit('/login')->on()->desktop()
        ->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->assertNoSmoke();

    stabilizeDesktopVisualState(visit('/checklists/runs/today')->on()->desktop())
        ->assertNoSmoke()
        ->assertPresent('.ops-screen--checklist-run')
        ->assertPresent('.ops-card')
        ->assertPresent('button[type="submit"], a[href*="incidents/new"]');

    stabilizeDesktopVisualState(visit('/incidents/new')->on()->desktop())
        ->assertNoSmoke()
        ->assertPresent('.ops-screen--staff-incident')
        ->assertPresent('input#title')
        ->assertPresent('textarea#description')
        ->assertPresent('button[type="submit"]');
});
