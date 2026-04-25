<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use Illuminate\Support\Facades\Route;

class DashboardWorkboardBuilder
{
    /**
     * @param  list<array{
     *     scope: string,
     *     scope_key: string,
     *     template_title: ?string,
     *     state: 'unavailable'|'not_started'|'in_progress'|'submitted',
     *     total_runs: int,
     *     submitted_runs: int,
     *     completion_percentage: int
     * }>  $scopeChecklistLanes
     * @param  list<array{
     *     title: string,
     *     description: string,
     *     count: int,
     *     actionLabel: string|null,
     *     url: string|null,
     *     tone: 'warning'|'danger'|'info'
     * }>  $attentionItems
     * @return array{
     *     state: 'attention'|'calm',
     *     headline: string,
     *     body: string,
     *     pendingLaneCount: int,
     *     attentionCount: int,
     *     submittedLaneCount: int,
     *     lanes: list<array{
     *         scope: string,
     *         scope_key: string,
     *         template_title: ?string,
     *         state: 'unavailable'|'not_started'|'in_progress'|'submitted',
     *         state_label: string,
     *         summary: string,
     *         total_runs: int,
     *         submitted_runs: int,
     *         completion_percentage: int
     *     }>,
     *     actions: list<array{
     *         label: string,
     *         url: string|null,
     *         tone: 'primary'|'secondary'
     *     }>
     * }
     */
    public function __invoke(array $scopeChecklistLanes, array $attentionItems): array
    {
        $pendingLanes = collect($scopeChecklistLanes)
            ->filter(fn (array $lane): bool => $lane['state'] !== 'submitted')
            ->map(fn (array $lane): array => [
                ...$lane,
                'state_label' => $this->stateLabel($lane['state']),
                'summary' => $this->laneSummary($lane),
            ])
            ->values()
            ->all();

        $pendingLaneCount = count($pendingLanes);
        $attentionCount = count($attentionItems);
        $submittedLaneCount = collect($scopeChecklistLanes)
            ->where('state', 'submitted')
            ->count();

        $state = ($pendingLaneCount === 0 && $attentionCount === 0)
            ? 'calm'
            : 'attention';

        return [
            'state' => $state,
            'headline' => $this->headline($state, $pendingLaneCount),
            'body' => $this->body($state, $pendingLaneCount, $attentionCount),
            'pendingLaneCount' => $pendingLaneCount,
            'attentionCount' => $attentionCount,
            'submittedLaneCount' => $submittedLaneCount,
            'lanes' => $pendingLanes,
            'actions' => $this->actions($state),
        ];
    }

    private function headline(string $state, int $pendingLaneCount): string
    {
        if ($state === 'calm') {
            return 'วันนี้ครอบคลุมครบและอยู่ในสภาพค่อนข้างนิ่ง';
        }

        return $pendingLaneCount > 0
            ? 'วันนี้ยังมีช่วงตรวจที่ยังเปิดค้างอยู่'
            : 'รายการตรวจเช็กปิดครบแล้ว แต่ยังมีงานติดตามค้างอยู่';
    }

    private function body(string $state, int $pendingLaneCount, int $attentionCount): string
    {
        if ($state === 'calm') {
            return 'ช่วงตรวจที่ใช้งานจริงทั้งหมดถูกตั้งค่าและส่งผลครบแล้ว ผู้ดูแลห้องแล็บสามารถใช้ประวัติและแนวโน้มเพื่อทบทวนงานได้โดยไม่ต้องเร่งแก้ปัญหาเฉพาะหน้า';
        }

        if ($pendingLaneCount > 0) {
            return sprintf(
                'วันนี้ยังมี %d ช่วงตรวจที่ต้องดำเนินการหรือส่งผลให้ครบ ใช้บอร์ดนี้เพื่อตรวจว่าจุดใดยังเปิดค้างอยู่ก่อนจะกลายเป็นงานสะสม',
                $pendingLaneCount,
            );
        }

        return sprintf(
            'แม้ว่ารายการตรวจเช็กจะปิดครบแล้ว แต่ยังมี %d สัญญาณที่ผู้ดูแลห้องแล็บต้องติดตามก่อนจะถือว่าวันนี้เรียบร้อยจริง',
            $attentionCount,
        );
    }

    /**
     * @return list<array{label: string, url: string|null, tone: 'primary'|'secondary'}>
     */
    private function actions(string $state): array
    {
        $actions = [];

        if ($state === 'attention') {
            $actions[] = [
                'label' => 'ดูคิวปัญหา',
                'url' => $this->routeOrNull('incidents.index'),
                'tone' => 'primary',
            ];
        }

        $actions[] = [
            'label' => 'ดูประวัติของวันนี้',
            'url' => $this->routeOrNull('checklists.history.index', ['runDate' => today()->toDateString()]),
            'tone' => 'secondary',
        ];

        return $actions;
    }

    private function stateLabel(string $state): string
    {
        return match ($state) {
            'unavailable' => 'ไม่มีแม่แบบใช้งานจริง',
            'not_started' => 'ยังไม่เริ่ม',
            'in_progress' => 'กำลังดำเนินการ',
            default => 'ส่งผลแล้ว',
        };
    }

    /**
     * @param  array{
     *     scope: string,
     *     template_title: ?string,
     *     state: 'unavailable'|'not_started'|'in_progress'|'submitted',
     *     total_runs: int,
     *     submitted_runs: int,
     *     completion_percentage: int
     * }  $lane
     */
    private function laneSummary(array $lane): string
    {
        return match ($lane['state']) {
            'unavailable' => 'ช่วงตรวจนี้ยังไม่มีแม่แบบใช้งานจริง จึงยังถือว่าการตั้งค่างานประจำวันไม่ครบ',
            'not_started' => 'มีแม่แบบใช้งานจริงแล้ว แต่ผู้ตรวจห้องยังไม่ได้เริ่มช่วงตรวจนี้ในวันนี้',
            'in_progress' => sprintf(
                'ส่งผลแล้ว %d จาก %d รอบ ช่วงตรวจนี้ถูกใช้งานแล้ว แต่ยังไม่ปิดงานครบ',
                $lane['submitted_runs'],
                $lane['total_runs'],
            ),
            default => 'ช่วงตรวจนี้ส่งผลเรียบร้อยแล้ว',
        };
    }

    /**
     * @param  array<string, string>  $parameters
     */
    private function routeOrNull(string $name, array $parameters = []): ?string
    {
        return Route::has($name) ? route($name, $parameters) : null;
    }
}
