<?php

use App\Application\Reports\Data\IncidentReportFilters;
use App\Application\Reports\Queries\BuildIncidentReport;
use App\Domain\Access\Enums\UserRole;
use App\Models\Incident;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('incident report aggregates date range room category and subcategory slices', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $roomA = $this->createRoom(['name' => 'Report Lab A', 'code' => 'RPT-A']);
    $roomB = $this->createRoom(['name' => 'Report Lab B', 'code' => 'RPT-B']);

    Incident::factory()->create([
        'title' => 'Internet outage in A',
        'category' => 'เครือข่าย',
        'subcategory' => 'อินเทอร์เน็ต',
        'severity' => 'High',
        'status' => 'Open',
        'room_id' => $roomA->id,
        'created_by' => $admin->id,
        'created_at' => '2026-05-02 09:00:00',
    ]);

    Incident::factory()->create([
        'title' => 'Wi-Fi unstable in A',
        'category' => 'เครือข่าย',
        'subcategory' => 'LAN/Wi-Fi',
        'severity' => 'Medium',
        'status' => 'Resolved',
        'room_id' => $roomA->id,
        'created_by' => $admin->id,
        'created_at' => '2026-05-03 09:00:00',
        'resolved_at' => '2026-05-03 13:00:00',
    ]);

    Incident::factory()->create([
        'title' => 'Printer issue in B',
        'category' => 'อุปกรณ์คอมพิวเตอร์',
        'subcategory' => 'เครื่องพิมพ์',
        'severity' => 'Low',
        'status' => 'Open',
        'room_id' => $roomB->id,
        'created_by' => $admin->id,
        'created_at' => '2026-05-04 09:00:00',
    ]);

    Incident::factory()->create([
        'title' => 'Outside selected range',
        'category' => 'เครือข่าย',
        'subcategory' => 'อินเทอร์เน็ต',
        'severity' => 'High',
        'status' => 'Open',
        'room_id' => $roomA->id,
        'created_by' => $admin->id,
        'created_at' => '2026-04-01 09:00:00',
    ]);

    $report = app(BuildIncidentReport::class)(new IncidentReportFilters(
        startDate: CarbonImmutable::parse('2026-05-01'),
        endDate: CarbonImmutable::parse('2026-05-05'),
        category: 'เครือข่าย',
    ));

    expect($report['summary']['total_count'])->toBe(2)
        ->and($report['summary']['unresolved_count'])->toBe(1)
        ->and($report['summary']['resolved_count'])->toBe(1)
        ->and($report['summary']['high_severity_count'])->toBe(1)
        ->and($report['summary']['rooms_impacted_count'])->toBe(1)
        ->and(collect($report['subcategory_rows'])->pluck('subcategory')->all())->toContain('อินเทอร์เน็ต', 'LAN/Wi-Fi')
        ->and(collect($report['room_rows'])->first()['room_name'])->toBe('Report Lab A')
        ->and(collect($report['recent_incidents'])->pluck('title')->all())->toContain('Internet outage in A', 'Wi-Fi unstable in A')
        ->and(collect($report['recent_incidents'])->pluck('title')->all())->not->toContain('Printer issue in B', 'Outside selected range');
});

test('incident report rejects mismatched subcategory filters by ignoring the invalid subcategory', function () {
    $admin = $this->createUserForRole(UserRole::Admin);

    Incident::factory()->create([
        'title' => 'Network item',
        'category' => 'เครือข่าย',
        'subcategory' => 'อินเทอร์เน็ต',
        'created_by' => $admin->id,
        'created_at' => '2026-05-02 09:00:00',
    ]);

    $report = app(BuildIncidentReport::class)(new IncidentReportFilters(
        startDate: CarbonImmutable::parse('2026-05-01'),
        endDate: CarbonImmutable::parse('2026-05-05'),
        category: 'เครือข่าย',
        subcategory: 'เครื่องพิมพ์',
    ));

    expect($report['filters']['subcategory'])->toBe('')
        ->and($report['summary']['total_count'])->toBe(1);
});
