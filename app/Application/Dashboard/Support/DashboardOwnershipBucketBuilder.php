<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use Illuminate\Support\Facades\Route;

class DashboardOwnershipBucketBuilder
{
    /**
     * @return array{
     *     state: 'active'|'calm',
     *     headline: string,
     *     body: string,
     *     buckets: list<array{
     *         key: 'overdue'|'unowned'|'mine',
     *         title: string,
     *         count: int,
     *         description: string,
     *         action_label: string,
     *         url: string|null,
     *         tone: 'danger'|'warning'|'info'
     *     }>
     * }
     */
    public function __invoke(int $unownedCount, int $overdueCount, int $ownedByActorCount): array
    {
        $state = ($unownedCount === 0 && $overdueCount === 0 && $ownedByActorCount === 0)
            ? 'calm'
            : 'active';

        return [
            'state' => $state,
            'headline' => $this->headline($unownedCount, $overdueCount, $ownedByActorCount),
            'body' => $this->body($state),
            'buckets' => [
                [
                    'key' => 'overdue',
                    'title' => 'ติดตามเกินกำหนด',
                    'count' => $overdueCount,
                    'description' => 'รายงานปัญหาที่ยังไม่ปิดและเลยวันที่เป้าหมายสำหรับการติดตามแล้ว',
                    'action_label' => 'ดูกลุ่มที่เลยกำหนดติดตาม',
                    'url' => $this->incidentsIndexUrl(['overdue' => 1]),
                    'tone' => 'danger',
                ],
                [
                    'key' => 'unowned',
                    'title' => 'ปัญหาที่ไม่มีผู้รับผิดชอบ',
                    'count' => $unownedCount,
                    'description' => 'รายงานปัญหาที่ยังไม่ปิดและยังไม่มีผู้ดูแลรับผิดชอบอย่างชัดเจน',
                    'action_label' => 'ดูกลุ่มปัญหาที่ไม่มีผู้รับผิดชอบ',
                    'url' => $this->incidentsIndexUrl(['unowned' => 1]),
                    'tone' => 'warning',
                ],
                [
                    'key' => 'mine',
                    'title' => 'งานที่คุณรับผิดชอบ',
                    'count' => $ownedByActorCount,
                    'description' => 'รายงานปัญหาที่ยังไม่ปิดและอยู่ในคิวที่คุณต้องขยับงานต่อ',
                    'action_label' => 'ดูปัญหาที่คุณรับผิดชอบ',
                    'url' => $this->incidentsIndexUrl(['mine' => 1]),
                    'tone' => 'info',
                ],
            ],
        ];
    }

    private function headline(int $unownedCount, int $overdueCount, int $ownedByActorCount): string
    {
        return match (true) {
            $overdueCount > 0 => 'งานติดตามเริ่มเลยเป้าหมายที่ตั้งไว้',
            $unownedCount > 0 => 'ยังมีรายงานปัญหาที่ไม่มีผู้รับผิดชอบชัดเจน',
            $ownedByActorCount > 0 => 'คิวงานที่คุณรับผิดชอบยังต้องทบทวนต่อ',
            default => 'ภาระเรื่องผู้รับผิดชอบยังอยู่ในระดับควบคุมได้',
        };
    }

    private function body(string $state): string
    {
        if ($state === 'calm') {
            return 'ตอนนี้ไม่มีรายงานปัญหาที่ยังไม่ปิดซึ่งไม่มีผู้รับผิดชอบ เลยกำหนดติดตาม หรือค้างอยู่กับคุณ ผู้ดูแลจึงยังอยู่ในโหมดทบทวนได้';
        }

        return 'ใช้กลุ่มงานเหล่านี้เพื่อตัดสินใจว่าควรกำหนดผู้รับผิดชอบ เร่งงานที่เลยกำหนด หรือปิดงานที่อยู่กับคุณก่อน';
    }

    /**
     * @param  array<string, int|string>  $parameters
     */
    private function incidentsIndexUrl(array $parameters): ?string
    {
        return Route::has('incidents.index')
            ? route('incidents.index', $parameters)
            : null;
    }
}
