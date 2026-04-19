<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest-facing home and login surfaces render without browser smoke issues', function () {
    [$homePage, $loginPage] = visit(['/', '/login']);

    $homePage
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Daily Ops Command Center')
        ->assertSee('Log in')
        ->assertSee('Suggested demo walkthrough')
        ->assertPresent('a[href="#main-content"]');

    $loginPage
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
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
        ->assertPresent('svg.ops-arc')
        ->assertPresent('svg.ops-sparkline')
        ->click('Checklist Templates')
        ->assertPathIs('/templates')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Checklist Templates')
        ->assertSee('Live runtime ownership by scope')
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
        ->assertSee('No invitation pipeline here')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->click('Checklist Templates')
        ->assertPathIs('/templates')
        ->click('Create template')
        ->assertPathIs('/templates/create')
        ->assertSee('Authoring pulse')
        ->assertSee('Live execution preview')
        ->assertSee('This is the scope lane currently selected in the governance form.')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
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
        ->assertSee('Review same day')
        ->assertSee('Review same scope')
        ->assertSee('Review same operator')
        ->assertSee('Opening checks')
        ->assertSee('Lamp issue')
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
        ->assertSee('Accountability Signals')
        ->assertSee('Checklist Trend')
        ->assertSee('Incident Intake Trend')
        ->assertSee('Operational Hotspots')
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

    $page = visit('/login');

    $page->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->click('Report Incident')
        ->assertPathIs('/incidents/new')
        ->fill('title', 'Browser smoke outcome incident')
        ->select('category', 'อื่น ๆ')
        ->select('severity', 'Low')
        ->fill('description', 'Testing the incident outcome surface.')
        ->click('Create Incident')
        ->assertSee('Submission Recap')
        ->assertSee('What Happens Next')
        ->assertPresent('.ops-recap-panel')
        ->assertSee('Report another incident')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});
