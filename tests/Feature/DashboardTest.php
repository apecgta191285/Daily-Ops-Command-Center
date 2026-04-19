<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;
use App\Models\Incident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('management users can visit the dashboard', function () {
    $this->seed();
    $user = User::where('role', UserRole::Admin->value)->first();
    $this->actingAs($user);

    $todayRuns = ChecklistRun::query()->whereDate('run_date', today())->count();
    $submittedTodayRuns = ChecklistRun::query()->whereDate('run_date', today())->whereNotNull('submitted_at')->count();
    $completionRate = $todayRuns > 0 ? (int) round(($submittedTodayRuns / $todayRuns) * 100) : 0;
    $openCount = Incident::query()->where('status', 'Open')->count();
    $inProgressCount = Incident::query()->where('status', 'In Progress')->count();
    $resolvedCount = Incident::query()->where('status', 'Resolved')->count();

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Checklist Completion Today');
    $response->assertSee("{$completionRate}%");
    $response->assertSee("{$submittedTodayRuns} of {$todayRuns} checklist runs submitted");
    $response->assertSee('ops-arc', false);
    $response->assertSee('ops-sparkline', false);
    $response->assertSee('Workboard Framing');
    $response->assertSee('Today-first workboard');
    $response->assertSee('Review today archive');
    $response->assertSee('Open Incidents');
    $response->assertSee((string) $openCount);
    $response->assertSee('In Progress');
    $response->assertSee((string) $inProgressCount);
    $response->assertSee('Resolved');
    $response->assertSee((string) $resolvedCount);
    $response->assertSee('Checklist by Scope');
    $response->assertSee('Recent Incidents');
});

test('supervisor can visit the dashboard', function () {
    $user = User::factory()->create(['role' => UserRole::Supervisor->value]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Checklist Completion Today');
});

test('staff users cannot visit the dashboard', function () {
    $user = User::factory()->create(['role' => UserRole::Staff->value]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertForbidden();
});

test('dashboard handles empty data without crashing', function () {
    $user = User::factory()->create(['role' => UserRole::Supervisor->value]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Checklist Completion Today');
    $response->assertSee('0%');
    $response->assertSee('0 of 0 checklist runs submitted');
    $response->assertSee('Today still has open operating lanes');
    $response->assertSee('Review today archive');
    $response->assertSee('Checklist by Scope');
    $response->assertSee('Missing live template');
    $response->assertSee('No incidents available yet.');
    $response->assertSee('Once staff report an issue, the latest incidents will appear here');
});

test('recent incidents are newest first limited to five and linked to details', function () {
    $this->seed();
    $user = User::where('role', UserRole::Admin->value)->first();
    $this->actingAs($user);

    $olderIncident = Incident::create([
        'title' => 'Very old incident',
        'category' => 'อื่น ๆ',
        'severity' => 'Low',
        'status' => 'Open',
        'description' => 'Old incident for dashboard ordering audit.',
        'created_by' => $user->id,
    ]);
    $olderIncident->forceFill([
        'created_at' => Carbon::now()->subDays(30),
        'updated_at' => Carbon::now()->subDays(30),
    ])->saveQuietly();

    $newestIncident = Incident::create([
        'title' => 'Newest incident for dashboard proof',
        'category' => 'เครือข่าย',
        'severity' => 'High',
        'status' => 'Open',
        'description' => 'Newest incident for dashboard ordering audit.',
        'created_by' => $user->id,
    ]);
    $newestIncident->forceFill([
        'created_at' => Carbon::now()->addMinute(),
        'updated_at' => Carbon::now()->addMinute(),
    ])->saveQuietly();

    $expectedRecentIncidents = Incident::query()
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->limit(5)
        ->get();

    $response = $this->get(route('dashboard'));
    $content = $response->getContent();

    $response->assertOk();
    $response->assertSee('Newest incident for dashboard proof');
    $response->assertDontSee('Very old incident');
    $response->assertSeeInOrder([
        $expectedRecentIncidents[0]->title,
        $expectedRecentIncidents[1]->title,
    ]);
    $response->assertSee(route('incidents.show', $newestIncident), false);
    expect(substr_count($content, 'View details'))->toBe(5);
});

test('dashboard attention panel highlights unresolved high severity and stale incidents', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin->value]);
    $this->actingAs($admin);

    Incident::factory()->create([
        'title' => 'High severity unresolved incident',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
        'created_by' => $admin->id,
    ]);

    $staleIncident = Incident::factory()->create([
        'title' => 'Stale unresolved incident',
        'severity' => IncidentSeverity::Medium->value,
        'status' => IncidentStatus::InProgress->value,
        'created_by' => $admin->id,
    ]);

    $staleIncident->forceFill([
        'created_at' => Carbon::now()->subDays(3),
        'updated_at' => Carbon::now()->subDays(3),
    ])->saveQuietly();

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Needs Attention Today');
    $response->assertSee('Active operating pressure');
    $response->assertSee('Today still has open operating lanes');
    $response->assertSee('Ownership and Work Buckets');
    $response->assertSee('History-Aware Command Layer');
    $response->assertSee('Review incidents you own');
    $response->assertSee('High severity incidents need attention');
    $response->assertSee('Review high severity incidents');
    $response->assertSee('Review today archive');
    $response->assertSee(route('incidents.index'), false);
    $response->assertSee('unresolved=1', false);
    $response->assertSee('severity=High', false);
    $response->assertSee('Unresolved incidents are going stale');
    $response->assertSee('stale=1', false);
});

test('dashboard shows ownership pressure signals and drill-down actions', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin->value]);
    $supervisor = User::factory()->create(['role' => UserRole::Supervisor->value]);
    $this->actingAs($admin);

    Incident::factory()->create([
        'title' => 'Needs owner dashboard incident',
        'severity' => IncidentSeverity::Medium->value,
        'status' => IncidentStatus::Open->value,
        'created_by' => $admin->id,
        'owner_id' => null,
    ]);

    Incident::factory()->create([
        'title' => 'Owned by admin dashboard incident',
        'severity' => IncidentSeverity::Low->value,
        'status' => IncidentStatus::InProgress->value,
        'created_by' => $supervisor->id,
        'owner_id' => $admin->id,
        'follow_up_due_at' => today()->addDay(),
    ]);

    Incident::factory()->create([
        'title' => 'Overdue dashboard incident',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
        'created_by' => $supervisor->id,
        'owner_id' => $supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Unowned incidents need accountability');
    $response->assertSee('Follow-up targets have already passed');
    $response->assertSee('Ownership and Work Buckets');
    $response->assertSee('Active accountability state');
    $response->assertSee('Follow-up has started slipping past target');
    $response->assertSee('History-Aware Command Layer');
    $response->assertSee('Recent carryover detected');
    $response->assertSee('Recent operating record still shows carryover');
    $response->assertSee('Overdue follow-up');
    $response->assertSee('Unowned incidents');
    $response->assertSee('Owned by you');
    $response->assertSee('Review unowned incidents');
    $response->assertSee('Review overdue follow-up');
    $response->assertSee('Review incidents you own');
    $response->assertSee('unowned=1', false);
    $response->assertSee('overdue=1', false);
    $response->assertSee('mine=1', false);
});

test('dashboard shows calm attention state when there are no active alerts', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin->value]);
    $this->actingAs($admin);

    ChecklistTemplate::query()->update(['is_active' => false]);

    $openingTemplate = $this->createTemplateWithItems([
        'title' => 'Calm opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $middayTemplate = $this->createTemplateWithItems([
        'title' => 'Calm midday template',
        'scope' => ChecklistScope::MIDDAY->value,
        'is_active' => true,
    ]);

    $closingTemplate = $this->createTemplateWithItems([
        'title' => 'Calm closing template',
        'scope' => ChecklistScope::CLOSING->value,
        'is_active' => true,
    ]);

    $this->createRunForUser($admin, $openingTemplate, submitted: true);
    $this->createRunForUser($admin, $middayTemplate, submitted: true);
    $this->createRunForUser($admin, $closingTemplate, submitted: true);

    Incident::factory()->create([
        'title' => 'Resolved historical incident',
        'severity' => IncidentSeverity::Low->value,
        'status' => IncidentStatus::Resolved->value,
        'created_by' => $admin->id,
        'resolved_at' => now(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('No urgent operational alerts right now.');
    $response->assertSee('Calm operating state');
    $response->assertSee('Today is covered and currently calm');
    $response->assertSee('No pending checklist lanes remain today.');
    $response->assertSee('Calm accountability state');
    $response->assertSee('Ownership pressure is currently under control');
    $response->assertSee('Stable recent record');
    $response->assertSee('Recent operating record looks settled');
    $response->assertDontSee('Review high severity incidents');
    $response->assertDontSee('Review stale incidents');
});

test('dashboard shows checklist and intake trends plus hotspot categories', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin->value]);
    $this->actingAs($admin);

    ChecklistRun::factory()->submitted($admin->id)->create([
        'created_by' => $admin->id,
        'run_date' => today(),
    ]);

    ChecklistRun::factory()->create([
        'created_by' => $admin->id,
        'run_date' => today(),
    ]);

    ChecklistRun::factory()->submitted($admin->id)->count(2)->create([
        'created_by' => $admin->id,
        'run_date' => today()->subDay(),
    ]);

    Incident::factory()->create([
        'title' => 'Today network issue',
        'category' => 'เครือข่าย',
        'status' => IncidentStatus::Open->value,
        'created_by' => $admin->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Incident::factory()->create([
        'title' => 'Today computer issue',
        'category' => 'อุปกรณ์คอมพิวเตอร์',
        'status' => IncidentStatus::Open->value,
        'created_by' => $admin->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $staleNetwork = Incident::factory()->create([
        'title' => 'Stale network issue',
        'category' => 'เครือข่าย',
        'status' => IncidentStatus::InProgress->value,
        'created_by' => $admin->id,
    ]);

    $staleNetwork->forceFill([
        'created_at' => Carbon::now()->subDays(3),
        'updated_at' => Carbon::now()->subDays(3),
    ])->saveQuietly();

    Incident::factory()->create([
        'title' => 'Yesterday network issue',
        'category' => 'เครือข่าย',
        'status' => IncidentStatus::Resolved->value,
        'created_by' => $admin->id,
        'resolved_at' => now()->subDay(),
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Checklist by Scope');
    $response->assertSee(ChecklistScope::OPENING->value);
    $response->assertSee('Missing live template');
    $response->assertSee('Checklist Trend');
    $response->assertSee('Yesterday: 100%');
    $response->assertSee('Down 50 points from yesterday');
    $response->assertSee('ops-sparkline', false);
    $response->assertSee('Incident Intake Trend');
    $response->assertSee('Yesterday: 1 reported');
    $response->assertSee('Up 1 incidents from yesterday');
    $response->assertSee('Operational Hotspots');
    $response->assertSee('เครือข่าย');
    $response->assertSee('2 unresolved');
    $response->assertSee('1 stale');
    $response->assertSee('data-hotspot-rank="1"', false);
    $response->assertSee('data-meter-target', false);
    $response->assertSee('category='.urlencode('เครือข่าย'), false);
});
