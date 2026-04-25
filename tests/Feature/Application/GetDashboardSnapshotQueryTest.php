<?php

declare(strict_types=1);

use App\Application\Dashboard\Queries\GetDashboardSnapshot;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistTemplate;
use App\Models\Incident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('dashboard snapshot query returns expected metrics and capped recent incidents', function () {
    $snapshot = app(GetDashboardSnapshot::class)();

    expect($snapshot->todayRuns)->toBeGreaterThanOrEqual(0);
    expect($snapshot->submittedTodayRuns)->toBeGreaterThanOrEqual(0);
    expect($snapshot->completionRate)->toBeGreaterThanOrEqual(0);
    expect($snapshot->incidentCounts)->toHaveKeys(['Open', 'In Progress', 'Resolved']);
    expect($snapshot->scopeChecklistLanes)->toHaveCount(count(ChecklistScope::cases()));
    expect($snapshot->recentIncidents->count())->toBeLessThanOrEqual(5);

    $expectedLatest = Incident::query()
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->first();

    expect($snapshot->recentIncidents->first()?->id)->toBe($expectedLatest?->id);
});

test('dashboard snapshot query exposes scope lane states for today', function () {
    $admin = User::factory()->create();

    ChecklistTemplate::query()->update(['is_active' => false]);

    $openingTemplate = $this->createTemplateWithItems([
        'title' => 'Opening snapshot template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $middayTemplate = $this->createTemplateWithItems([
        'title' => 'Midday snapshot template',
        'scope' => ChecklistScope::MIDDAY->value,
        'is_active' => true,
    ]);

    $this->createRunForUser($admin, $openingTemplate, submitted: true);
    $this->createRunForUser($admin, $middayTemplate, submitted: false);

    $snapshot = app(GetDashboardSnapshot::class)();

    $lanes = collect($snapshot->scopeChecklistLanes)->keyBy('scope');

    expect($lanes[ChecklistScope::OPENING->value]['state'])->toBe('submitted');
    expect($lanes[ChecklistScope::OPENING->value]['submitted_runs'])->toBeGreaterThanOrEqual(1);
    expect($lanes[ChecklistScope::MIDDAY->value]['state'])->toBe('in_progress');
    expect($lanes[ChecklistScope::MIDDAY->value]['total_runs'])->toBeGreaterThanOrEqual(1);
    expect($lanes[ChecklistScope::CLOSING->value]['state'])->toBe('unavailable');
});

test('dashboard snapshot query exposes ownership pressure summary', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $supervisor = User::factory()->create(['role' => 'supervisor']);

    Incident::factory()->create([
        'title' => 'Unowned dashboard incident',
        'status' => 'Open',
        'created_by' => $admin->id,
        'owner_id' => null,
    ]);

    Incident::factory()->create([
        'title' => 'Owned by admin dashboard incident',
        'status' => 'In Progress',
        'created_by' => $supervisor->id,
        'owner_id' => $admin->id,
        'follow_up_due_at' => today()->addDay(),
    ]);

    Incident::factory()->create([
        'title' => 'Overdue dashboard incident',
        'status' => 'Open',
        'created_by' => $supervisor->id,
        'owner_id' => $supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    $snapshot = app(GetDashboardSnapshot::class)($admin->id);

    expect($snapshot->ownershipPressure['unownedCount'])->toBeGreaterThanOrEqual(1);
    expect($snapshot->ownershipPressure['overdueCount'])->toBeGreaterThanOrEqual(1);
    expect($snapshot->ownershipPressure['ownedByActorCount'])->toBeGreaterThanOrEqual(1);
    expect(collect($snapshot->ownershipPressure['actions'])->pluck('label')->all())
        ->toContain('ดูกลุ่มปัญหาที่ไม่มีผู้รับผิดชอบ', 'ดูกลุ่มที่เลยกำหนดติดตาม', 'ดูปัญหาที่คุณรับผิดชอบ');
    expect($snapshot->ownershipBuckets['state'])->toBe('active');
    expect($snapshot->ownershipBuckets['headline'])->toBe('งานติดตามเริ่มเลยเป้าหมายที่ตั้งไว้');
    expect(collect($snapshot->ownershipBuckets['buckets'])->pluck('title')->all())
        ->toBe(['ติดตามเกินกำหนด', 'ปัญหาที่ไม่มีผู้รับผิดชอบ', 'งานที่คุณรับผิดชอบ']);
});

test('dashboard snapshot query exposes recent history context', function () {
    $admin = User::factory()->create();

    ChecklistTemplate::query()->update(['is_active' => false]);

    $openingTemplate = $this->createTemplateWithItems([
        'title' => 'History-aware opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $run = $this->createRunForUser(
        $admin,
        $openingTemplate,
        submitted: true,
        itemStates: [
            ['result' => 'Not Done', 'note' => 'Carryover issue'],
        ],
        runDate: now()->subDay()->toDateString(),
    );

    $run->loadCount([
        'items',
        'items as not_done_items_count' => fn ($query) => $query->where('result', 'Not Done'),
        'items as noted_items_count' => fn ($query) => $query->whereNotNull('note'),
    ]);

    Incident::factory()->create([
        'title' => 'Recent still-active incident',
        'status' => 'Open',
        'created_by' => $admin->id,
        'created_at' => now()->subDay(),
    ]);

    $snapshot = app(GetDashboardSnapshot::class)($admin->id);

    expect($snapshot->recentHistoryContext['state'])->toBe('unstable');
    expect($snapshot->recentHistoryContext['headline'])->toBe('ประวัติล่าสุดยังมีงานค้างต่อเนื่อง');
    expect($snapshot->recentHistoryContext['archive']['focus_date'])->toBe(now()->subDay()->toDateString());
    expect($snapshot->recentHistoryContext['archive']['total_not_done_items'])->toBeGreaterThanOrEqual(1);
    expect($snapshot->recentHistoryContext['incidents']['opened_count'])->toBeGreaterThanOrEqual(1);
});

test('dashboard snapshot query keeps incident intake counts on the correct day boundaries', function () {
    Carbon::setTestNow(Carbon::parse('2026-04-23 10:00:00'));

    $admin = User::factory()->create(['role' => 'admin']);

    $justBeforeToday = Incident::factory()->create([
        'title' => 'Boundary incident before today',
        'status' => 'Open',
        'created_by' => $admin->id,
    ]);

    $justBeforeToday->forceFill([
        'created_at' => Carbon::parse('2026-04-22 23:59:59'),
        'updated_at' => Carbon::parse('2026-04-22 23:59:59'),
    ])->saveQuietly();

    $startOfToday = Incident::factory()->create([
        'title' => 'Boundary incident at start of today',
        'status' => 'Open',
        'created_by' => $admin->id,
    ]);

    $startOfToday->forceFill([
        'created_at' => Carbon::parse('2026-04-23 00:00:00'),
        'updated_at' => Carbon::parse('2026-04-23 00:00:00'),
    ])->saveQuietly();

    $snapshot = app(GetDashboardSnapshot::class)($admin->id);

    $series = $snapshot->incidentIntakeTrend['series'];
    $todayCount = Incident::query()
        ->where('created_at', '>=', Carbon::parse('2026-04-23 00:00:00'))
        ->where('created_at', '<', Carbon::parse('2026-04-24 00:00:00'))
        ->count();
    $yesterdayCount = Incident::query()
        ->where('created_at', '>=', Carbon::parse('2026-04-22 00:00:00'))
        ->where('created_at', '<', Carbon::parse('2026-04-23 00:00:00'))
        ->count();

    expect($series)->toHaveCount(7);
    expect($series[array_key_last($series)])->toBe($todayCount);
    expect($series[array_key_last($series) - 1])->toBe($yesterdayCount);

    Carbon::setTestNow();
});
