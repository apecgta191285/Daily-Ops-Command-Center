<?php

use App\Application\Dashboard\Support\DashboardHotspotAssembler;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

uses(TestCase::class);

test('dashboard hotspot assembler maps aggregated rows into review-ready summaries', function () {
    Route::get('/incidents', fn () => 'ok')->name('incidents.index');

    $rows = collect([
        (object) [
            'category' => 'เครือข่าย',
            'unresolved_count' => 2,
            'stale_count' => 1,
        ],
    ]);

    $hotspots = app(DashboardHotspotAssembler::class)($rows);

    expect($hotspots)->toBe([
        [
            'category' => 'เครือข่าย',
            'unresolvedCount' => 2,
            'staleCount' => 1,
            'url' => route('incidents.index', ['unresolved' => 1, 'category' => 'เครือข่าย']),
        ],
    ]);
});
