<?php

declare(strict_types=1);

use App\Application\Dashboard\Support\DashboardAttentionAssembler;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

uses(TestCase::class);

test('dashboard attention assembler builds checklist and incident attention items', function () {
    Route::get('/incidents', fn () => 'ok')->name('incidents.index');

    $items = app(DashboardAttentionAssembler::class)(
        todayRuns: 5,
        submittedTodayRuns: 3,
        completionRate: 60,
        highSeverityUnresolvedCount: 2,
        staleUnresolvedCount: 1,
        unownedUnresolvedCount: 1,
        overdueFollowUpCount: 1,
        scopeLanesMissingTemplateCount: 0,
        scopeLanesIncompleteCount: 0,
    );

    expect($items)->toHaveCount(5)
        ->and($items[0]['title'])->toBe('การตรวจเช็กวันนี้ยังดำเนินการไม่ครบ')
        ->and($items[0]['count'])->toBe(2)
        ->and($items[1]['title'])->toBe('มีรายงานปัญหาความรุนแรงสูงที่ต้องรีบดู')
        ->and($items[1]['url'])->toContain('severity=High')
        ->and($items[2]['title'])->toBe('มีรายงานปัญหาที่ยังค้างนานเกินควร')
        ->and($items[2]['url'])->toContain('stale=1')
        ->and($items[3]['title'])->toBe('มีรายงานปัญหาที่ไม่มีผู้รับผิดชอบ')
        ->and($items[3]['url'])->toContain('unowned=1')
        ->and($items[4]['title'])->toBe('มีรายงานปัญหาที่เลยกำหนดติดตามแล้ว')
        ->and($items[4]['url'])->toContain('overdue=1');
});

test('dashboard attention assembler adds scope lane warnings when coverage is missing or incomplete', function () {
    $items = app(DashboardAttentionAssembler::class)(
        todayRuns: 2,
        submittedTodayRuns: 2,
        completionRate: 100,
        highSeverityUnresolvedCount: 0,
        staleUnresolvedCount: 0,
        unownedUnresolvedCount: 0,
        overdueFollowUpCount: 0,
        scopeLanesMissingTemplateCount: 1,
        scopeLanesIncompleteCount: 2,
    );

    expect($items)->toHaveCount(2)
        ->and($items[0]['title'])->toBe('มีรอบเวลาที่ไม่มีแม่แบบใช้งานจริง')
        ->and($items[0]['count'])->toBe(1)
        ->and($items[1]['title'])->toBe('บางช่วงตรวจของวันนี้ยังไม่เสร็จ')
        ->and($items[1]['count'])->toBe(2);
});
