<?php

declare(strict_types=1);

use App\Application\Dashboard\Support\DashboardOwnershipBucketBuilder;
use Tests\TestCase;

uses(TestCase::class);

test('dashboard ownership bucket builder creates actionable buckets and calm state', function () {
    $builder = app(DashboardOwnershipBucketBuilder::class);

    $active = $builder(unownedCount: 2, overdueCount: 1, ownedByActorCount: 3);

    expect($active['state'])->toBe('active');
    expect($active['headline'])->toBe('งานติดตามเริ่มเลยเป้าหมายที่ตั้งไว้');
    expect($active['buckets'])->toHaveCount(3);
    expect(collect($active['buckets'])->pluck('title')->all())
        ->toBe(['ติดตามเกินกำหนด', 'ปัญหาที่ไม่มีผู้รับผิดชอบ', 'งานที่คุณรับผิดชอบ']);
    expect(collect($active['buckets'])->pluck('count', 'key')->all())
        ->toMatchArray([
            'overdue' => 1,
            'unowned' => 2,
            'mine' => 3,
        ]);

    $calm = $builder(unownedCount: 0, overdueCount: 0, ownedByActorCount: 0);

    expect($calm['state'])->toBe('calm');
    expect($calm['headline'])->toBe('ภาระเรื่องผู้รับผิดชอบยังอยู่ในระดับควบคุมได้');
});
