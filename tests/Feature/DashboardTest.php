<?php

use App\Domain\Access\Enums\UserRole;
use App\Models\ChecklistRun;
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
    $response->assertSee('Open Incidents');
    $response->assertSee((string) $openCount);
    $response->assertSee('In Progress');
    $response->assertSee((string) $inProgressCount);
    $response->assertSee('Resolved');
    $response->assertSee((string) $resolvedCount);
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
    $response->assertSee('No incidents available yet.');
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
