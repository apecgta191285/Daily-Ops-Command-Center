<?php

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
    );

    expect($items)->toHaveCount(3)
        ->and($items[0]['title'])->toBe('Checklist completion is still in progress')
        ->and($items[0]['count'])->toBe(2)
        ->and($items[1]['title'])->toBe('High severity incidents need attention')
        ->and($items[1]['url'])->toContain('severity=High')
        ->and($items[2]['title'])->toBe('Unresolved incidents are going stale')
        ->and($items[2]['url'])->toContain('stale=1');
});
