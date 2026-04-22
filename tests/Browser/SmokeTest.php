<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function stabilizeVisualState($page)
{
    $page->script(<<<'JS'
        if (! document.getElementById('browser-qa-disable-motion')) {
            const style = document.createElement('style');
            style.id = 'browser-qa-disable-motion';
            style.textContent = `
                *,
                *::before,
                *::after {
                    animation: none !important;
                    transition: none !important;
                    caret-color: auto !important;
                }

                [data-motion],
                [data-motion] *,
                [data-motion] *::before,
                [data-motion] *::after {
                    opacity: 1 !important;
                    transform: none !important;
                    filter: none !important;
                }

                .auth-panel,
                .auth-panel *,
                .ops-card,
                .ops-card *,
                .ops-hero,
                .ops-hero *,
                .ops-workboard,
                .ops-workboard *,
                .ops-bucket-board,
                .ops-bucket-board *,
                .ops-context-board,
                .ops-context-board *,
                .ops-incident-panel,
                .ops-incident-panel *,
                .ops-incident-lane,
                .ops-incident-lane * {
                    opacity: 1 !important;
                    transform: none !important;
                    filter: none !important;
                }
            `;

            document.head.appendChild(style);
        }

        document.querySelectorAll('[data-motion]').forEach((element) => {
            element.classList.add('is-visible');
            element.style.transition = 'none';
            element.style.opacity = '1';
            element.style.transform = 'none';
            element.style.filter = 'none';
        });
        document.querySelectorAll('[data-meter-target]').forEach((meter) => {
            meter.style.transition = 'none';
            meter.style.width = `${meter.dataset.meterTarget ?? '0'}%`;
        });

        if (document.activeElement instanceof HTMLElement) {
            document.activeElement.blur();
        }
    JS);

    return $page->wait(0.35);
}

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-04-20 09:00:00'));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('guest-facing home and login surfaces render without browser smoke issues', function () {
    [$homePage, $loginPage] = visit(['/', '/login']);

    $homePage
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertNoAccessibilityIssues()
        ->assertSee('Daily Ops Command Center')
        ->assertSee('University Computer Lab Daily Ops')
        ->assertSee('Log in')
        ->assertSee('Suggested demo walkthrough')
        ->assertPresent('a[href="#main-content"]');

    $loginPage
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertNoAccessibilityIssues()
        ->assertSee('Log in to your account')
        ->assertSee('Local demo accounts')
        ->assertPresent('input[name="email"]')
        ->assertPresent('a[href="#main-content"]')
        ->assertPresent('[data-test="login-button"]');
});

test('admin can authenticate and reach checklist template administration in the main app shell', function () {
    $admin = $this->createUserForRole(UserRole::Admin);

    $this->createTemplateWithItems([
        'title' => 'Browser smoke template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $page = visit('/login');

    $page->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertPresent('body > [data-flux-sidebar]')
        ->assertPresent('body > [data-flux-header]')
        ->assertPresent('body > [data-flux-main]')
        ->assertSee('Dashboard')
        ->assertSee('Workboard Framing')
        ->assertSee('Ownership and Work Buckets')
        ->assertSee('History-Aware Command Layer')
        ->assertSee('Review today archive')
        ->assertPresent('svg.ops-arc')
        ->assertPresent('svg.ops-sparkline')
        ->click('Checklist Templates')
        ->assertPathIs('/templates')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Checklist Templates')
        ->assertSee('Live checklist ownership by scope')
        ->assertSee('Live covered')
        ->assertSee('Create template')
        ->assertPresent('tr[data-template-active="true"]')
        ->click('Users')
        ->assertPathIs('/users')
        ->assertSee('Team Access Roster')
        ->assertSee('Coverage by role lane')
        ->assertSee('Create user')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->click('Create user')
        ->assertPathIs('/users/create')
        ->assertSee('Create User Account')
        ->assertSee('No invitation email flow here')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->click('Checklist Templates')
        ->assertPathIs('/templates')
        ->click('Create template')
        ->assertPathIs('/templates/create')
        ->assertSee('Draft check')
        ->assertSee('Live execution preview')
        ->assertSee('This is the scope lane currently selected in the governance form.')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('guest desktop screenshot baselines hold for home and login', function () {
    [$homePage, $loginPage] = visit(['/', '/login']);

    stabilizeVisualState($homePage)
        ->assertNoSmoke()
        ->assertScreenshotMatches();

    stabilizeVisualState($loginPage)
        ->assertNoSmoke()
        ->assertScreenshotMatches();
});

test('guest mobile browser coverage holds for home and login', function () {
    $homePage = visit('/')->on()->mobile();
    $loginPage = visit('/login')->on()->mobile();

    stabilizeVisualState($homePage)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertScreenshotMatches();

    stabilizeVisualState($loginPage)
        ->assertNoSmoke()
        ->assertScreenshotMatches();
});

test('admin governance screenshot and accessibility baselines hold for deterministic authenticated surfaces', function () {
    $admin = $this->createUserForRole(UserRole::Admin, ['name' => 'Browser Governance Admin']);
    $this->createUserForRole(UserRole::Supervisor, ['name' => 'Browser Governance Supervisor']);
    $this->createUserForRole(UserRole::Staff, ['name' => 'Browser Governance Staff']);

    $this->createTemplateWithItems([
        'title' => 'Browser governance opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $dashboardPage = visit('/login');

    $dashboardPage->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoSmoke();

    $uiGovernancePage = visit('/ui-governance');

    stabilizeVisualState($uiGovernancePage)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertSee('UI Contract Guide')
        ->assertSee('Screen QA checklist')
        ->assertScreenshotMatches();

    $templatePage = visit('/templates');

    $templatePage
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertSee('Checklist Templates')
        ->assertSee('Live checklist ownership by scope');

    $userPage = visit('/users');

    $userPage
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertSee('Team Access Roster')
        ->assertSee('Coverage by role lane');
});

test('dashboard screenshot baselines hold for desktop and mobile', function () {
    $admin = $this->createUserForRole(UserRole::Admin, ['name' => 'Dashboard Snapshot Admin']);
    $supervisor = $this->createUserForRole(UserRole::Supervisor, ['name' => 'Dashboard Snapshot Supervisor']);

    $this->createTemplateWithItems([
        'title' => 'Dashboard snapshot opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $this->createIncidentWithActivity($admin, [
        'title' => 'Dashboard snapshot hotspot incident',
        'category' => 'เครือข่าย',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
    ]);

    $this->createIncidentWithActivity($supervisor, [
        'title' => 'Dashboard snapshot overdue follow-up',
        'category' => 'ความปลอดภัย',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
        'owner_id' => $supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoSmoke();

    $dashboardDesktop = visit('/dashboard')->on()->desktop();
    $dashboardMobile = visit('/dashboard')->on()->mobile();

    stabilizeVisualState($dashboardDesktop)
        ->assertNoSmoke()
        ->assertSee('Workboard Framing')
        ->assertSee('Room issue hotspots')
        ->assertScreenshotMatches();

    stabilizeVisualState($dashboardMobile)
        ->assertNoSmoke()
        ->assertSee('Workboard Framing')
        ->assertSee('Room issue hotspots')
        ->assertScreenshotMatches();
});

test('template authoring smoke coverage holds for desktop and mobile', function () {
    $admin = $this->createUserForRole(UserRole::Admin, ['name' => 'Template Snapshot Admin']);
    $this->createTemplateWithItems([
        'title' => 'Template snapshot opening checklist',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoSmoke();

    $templateDesktop = visit('/templates/create')->on()->desktop();
    $templateMobile = visit('/templates/create')->on()->mobile();

    stabilizeVisualState($templateDesktop)
        ->assertNoSmoke()
        ->assertSee('Build the live checklist in three passes')
        ->assertSee('Core definition');

    stabilizeVisualState($templateMobile)
        ->assertNoSmoke()
        ->assertSee('Build the live checklist in three passes')
        ->assertSee('Core definition');
});

test('checklist runtime screenshot baselines hold for desktop and mobile', function () {
    $staff = $this->createUserForRole(UserRole::Staff, ['name' => 'Checklist Snapshot Staff']);

    $this->createTemplateWithItems([
        'title' => 'Checklist snapshot template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    visit('/login')
        ->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->assertNoSmoke();

    $checklistDesktop = visit('/checklists/runs/today')->on()->desktop();
    $checklistMobile = visit('/checklists/runs/today')->on()->mobile();

    stabilizeVisualState($checklistDesktop)
        ->assertNoSmoke()
        ->assertSee('Today\'s Progress')
        ->assertSee('Recent Submission Context')
        ->assertScreenshotMatches();

    stabilizeVisualState($checklistMobile)
        ->assertNoSmoke()
        ->assertSee('Today\'s Progress')
        ->assertSee('Recent Submission Context')
        ->assertScreenshotMatches();
});

test('incident detail smoke coverage holds for desktop and mobile', function () {
    $supervisor = $this->createUserForRole(UserRole::Supervisor, ['name' => 'Incident Snapshot Supervisor']);

    $incident = $this->createIncidentWithActivity($supervisor, [
        'title' => 'Incident snapshot detail record',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::InProgress->value,
    ]);

    visit('/login')
        ->fill('email', $supervisor->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoSmoke();

    $incidentDesktop = visit(route('incidents.show', $incident))->on()->desktop();
    $incidentMobile = visit(route('incidents.show', $incident))->on()->mobile();

    stabilizeVisualState($incidentDesktop)
        ->assertNoSmoke()
        ->assertSee('Description and evidence')
        ->assertSee('Accountability lane');

    stabilizeVisualState($incidentMobile)
        ->assertNoSmoke()
        ->assertSee('Description and evidence')
        ->assertSee('Accountability lane');
});

test('management dashboard drill-down links lead to filtered incident follow-up views', function () {
    $supervisor = $this->createUserForRole(UserRole::Supervisor);

    $this->createIncidentWithActivity($supervisor, [
        'title' => 'High severity browser incident',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
    ]);

    $page = visit('/login');

    $page->fill('email', $supervisor->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertSee('Needs Attention Today')
        ->click('Review high severity incidents')
        ->assertPathBeginsWith('/incidents')
        ->assertSee('Active filter context:')
        ->assertSee('Unresolved only')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management incident queue shows accountability filters and fields without browser smoke issues', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $supervisor = $this->createUserForRole(UserRole::Supervisor);

    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser owned accountability incident',
        'status' => IncidentStatus::InProgress->value,
        'owner_id' => $admin->id,
        'follow_up_due_at' => today()->addDay(),
    ]);

    $this->createIncidentWithActivity($supervisor, [
        'title' => 'Browser overdue accountability incident',
        'status' => IncidentStatus::Open->value,
        'owner_id' => $supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->click('Incidents')
        ->assertPathIs('/incidents')
        ->assertSee('Only unowned incidents')
        ->assertSee('Only my incidents')
        ->assertSee('Only overdue follow-up')
        ->assertSee('Owner')
        ->assertSee('Follow-up')
        ->assertNoAccessibilityIssues()
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management can browse incident history slices without browser smoke issues', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $owner = $this->createUserForRole(UserRole::Supervisor, ['name' => 'Browser History Owner']);

    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser incident history open record',
        'status' => IncidentStatus::Open->value,
        'severity' => IncidentSeverity::High->value,
        'owner_id' => $owner->id,
        'created_at' => now()->subDay(),
    ]);

    $this->createIncidentWithActivity($owner, [
        'title' => 'Browser incident history resolved record',
        'status' => IncidentStatus::Resolved->value,
        'severity' => IncidentSeverity::Medium->value,
        'owner_id' => $owner->id,
        'created_at' => now()->subDays(2),
        'resolved_at' => now()->subDay(),
    ]);

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->click('Incident History')
        ->assertPathIs('/incidents/history')
        ->assertSee('Incident History')
        ->assertSee('Recent incident movement')
        ->assertSee('Still active')
        ->assertSee('Browser incident history open record')
        ->assertSee('Browser incident history resolved record')
        ->assertNoAccessibilityIssues()
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management can browse checklist run archive without browser smoke issues', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $operator = $this->createUserForRole(UserRole::Staff, ['name' => 'Archive Browser Operator']);

    $template = $this->createTemplateWithItems([
        'title' => 'Browser archive template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ], [
        ['title' => 'Unlock room', 'description' => 'Prepare lab', 'group_label' => 'Opening checks'],
        ['title' => 'Check projector', 'description' => 'Verify display', 'group_label' => 'Opening checks'],
    ]);

    $this->createRunForUser(
        $operator,
        $template,
        submitted: true,
        itemStates: [
            ['result' => 'Done', 'note' => null],
            ['result' => 'Not Done', 'note' => 'Lamp issue'],
        ],
        runDate: now()->subDay()->toDateString(),
    );

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->click('Run History')
        ->assertPathIs('/checklists/history')
        ->assertSee('Checklist Run Archive')
        ->assertSee('Archive day context')
        ->assertSee('Submitted runs only')
        ->assertSee('Browser archive template')
        ->assertSee('More from operator')
        ->assertSee('View recap')
        ->click('View recap')
        ->assertPathBeginsWith('/checklists/history/')
        ->assertSee('Historical recap')
        ->assertSee('Follow-up worth reviewing')
        ->assertSee('Printable recap')
        ->assertSee('Review same day')
        ->assertSee('Review same scope')
        ->assertSee('Review same operator')
        ->assertSee('Opening checks')
        ->assertSee('Lamp issue')
        ->assertNoAccessibilityIssues()
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management incident detail exposes printable evidence summary without browser smoke issues', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $owner = $this->createUserForRole(UserRole::Supervisor, ['name' => 'Printable Browser Owner']);

    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser printable issue record',
        'status' => IncidentStatus::InProgress->value,
        'severity' => IncidentSeverity::High->value,
        'owner_id' => $owner->id,
        'follow_up_due_at' => today()->addDay(),
    ]);

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->click('Incidents')
        ->assertPathIs('/incidents')
        ->click('View details')
        ->assertSee('Printable summary')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management dashboard shows trend and hotspot sections without browser smoke issues', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->createTemplateWithItems([
        'title' => 'Opening browser scope template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);
    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser hotspot incident one',
        'category' => 'เครือข่าย',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
    ]);
    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser hotspot incident two',
        'category' => 'เครือข่าย',
        'severity' => IncidentSeverity::Medium->value,
        'status' => IncidentStatus::InProgress->value,
    ]);
    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser unowned accountability incident',
        'category' => 'อื่น ๆ',
        'severity' => IncidentSeverity::Medium->value,
        'status' => IncidentStatus::Open->value,
        'owner_id' => null,
    ]);
    $this->createIncidentWithActivity($supervisor, [
        'title' => 'Browser overdue accountability pressure',
        'category' => 'ความปลอดภัย',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
        'owner_id' => $supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    $page = visit('/login');

    $page->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertSee('Checklist by Scope')
        ->assertSee('Ownership and Work Buckets')
        ->assertSee('History-Aware Command Layer')
        ->assertSee('Checklist Trend')
        ->assertSee('Incident Intake Trend')
        ->assertSee('Room issue hotspots')
        ->assertSee('Review unowned incidents')
        ->assertSee('Review overdue follow-up')
        ->assertPresent('svg.ops-arc')
        ->assertPresent('svg.ops-sparkline')
        ->assertPresent('[data-meter-target]')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management incident detail reads as a narrative surface without browser smoke issues', function () {
    $supervisor = $this->createUserForRole(UserRole::Supervisor);

    $incident = $this->createIncidentWithActivity($supervisor, [
        'title' => 'Narrative incident detail smoke',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::InProgress->value,
    ]);

    $incident->activities()->create([
        'action_type' => 'next_action_note',
        'summary' => 'Next action: Review this incident through the narrative surface.',
        'actor_id' => $supervisor->id,
        'created_at' => now()->addMinutes(20),
    ]);

    visit('/login')
        ->fill('email', $supervisor->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->click('Incidents')
        ->assertPathIs('/incidents')
        ->click('View details')
        ->assertSee('Latest handling context')
        ->assertSee('Description and evidence')
        ->assertSee('Accountability lane')
        ->assertSee('Update status with intent')
        ->assertSee('Activity timeline')
        ->assertPresent('select[wire\\:model="ownerId"]')
        ->assertPresent('input[wire\\:model="followUpDueAt"]')
        ->assertPresent('[data-severity="High"]')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('staff can authenticate into the daily checklist workflow without browser smoke issues', function () {
    $staff = $this->createUserForRole(UserRole::Staff);

    $this->createTemplateWithItems([
        'title' => 'Staff browser smoke template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $page = visit('/login');

    $page->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertPresent('body > [data-flux-main]')
        ->assertPresent('#main-content[data-flux-main]')
        ->assertSee('Daily Checklist')
        ->assertSee('Today\'s Progress')
        ->assertSee('Recent Submission Context')
        ->assertSee('Report Incident')
        ->assertSee('Submit Checklist');
});

test('staff incident reporting shows a post-submit outcome screen', function () {
    $staff = $this->createUserForRole(UserRole::Staff);
    $room = $this->createRoom([
        'name' => 'Browser Room 1',
        'code' => 'LAB-B1',
    ]);

    $page = visit('/login');

    $page->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->click('Report Incident')
        ->assertPathIs('/incidents/new')
        ->fill('title', 'Browser smoke outcome incident')
        ->select('category', 'อื่น ๆ')
        ->assertSee($room->name)
        ->select('severity', 'Low')
        ->fill('description', 'Testing the incident outcome surface.')
        ->click('Create incident')
        ->assertSee('Submission Recap')
        ->assertSee('What Happens Next')
        ->assertPresent('.ops-recap-panel')
        ->assertSee('Report another incident')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('staff can choose a room before entering the live checklist lane', function () {
    $staff = $this->createUserForRole(UserRole::Staff, ['name' => 'Room Choice Staff']);

    $this->createRoom([
        'name' => 'Browser Lab 1',
        'code' => 'LAB-B1',
    ]);

    $this->createRoom([
        'name' => 'Browser Lab 2',
        'code' => 'LAB-B2',
    ]);

    $this->createTemplateWithItems([
        'title' => 'Browser room choice opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    visit('/login')
        ->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/checklists/runs/today')
        ->assertSee('Choose today\'s lab room first')
        ->assertSee('Browser Lab 1')
        ->assertSee('Browser Lab 2')
        ->click('Use this room')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->assertSee('Browser Lab 1')
        ->assertSee('Submit Checklist')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});
