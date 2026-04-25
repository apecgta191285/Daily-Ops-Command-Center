<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use App\Application\Incidents\Support\IncidentStalePolicy;
use Illuminate\Support\Facades\Route;

class DashboardAttentionAssembler
{
    /**
     * @return list<array{
     *     title: string,
     *     description: string,
     *     count: int,
     *     actionLabel: string|null,
     *     url: string|null,
     *     tone: 'warning'|'danger'|'info'
     * }>
     */
    public function __invoke(
        int $todayRuns,
        int $submittedTodayRuns,
        int $completionRate,
        int $highSeverityUnresolvedCount,
        int $staleUnresolvedCount,
        int $unownedUnresolvedCount,
        int $overdueFollowUpCount,
        int $scopeLanesMissingTemplateCount,
        int $scopeLanesIncompleteCount,
    ): array {
        $attentionItems = [];

        if ($todayRuns === 0) {
            $attentionItems[] = [
                'title' => 'วันนี้ยังไม่มีรอบการตรวจเช็กถูกสร้าง',
                'description' => 'ผู้ตรวจห้องยังไม่ได้เริ่มรายการตรวจเช็กของวันนี้ จึงยังติดตามความครบถ้วนของงานประจำวันไม่ได้',
                'count' => 0,
                'actionLabel' => null,
                'url' => null,
                'tone' => 'warning',
            ];
        } elseif ($completionRate < 100) {
            $attentionItems[] = [
                'title' => 'การตรวจเช็กวันนี้ยังดำเนินการไม่ครบ',
                'description' => sprintf(
                    'วันนี้ส่งรอบการตรวจเช็กแล้ว %d จาก %d รอบ หากทีมควรทำงานเสร็จแล้ว ควรติดตามต่อทันที',
                    $submittedTodayRuns,
                    $todayRuns,
                ),
                'count' => $todayRuns - $submittedTodayRuns,
                'actionLabel' => null,
                'url' => null,
                'tone' => 'info',
            ];
        }

        if ($scopeLanesMissingTemplateCount > 0) {
            $attentionItems[] = [
                'title' => 'มีรอบเวลาที่ไม่มีแม่แบบใช้งานจริง',
                'description' => 'ยังมีอย่างน้อยหนึ่งช่วงตรวจที่ไม่มีแม่แบบใช้งานจริง ทำให้ยืนยันไม่ได้ว่างานประจำวันถูกตั้งค่าครบแล้ว',
                'count' => $scopeLanesMissingTemplateCount,
                'actionLabel' => null,
                'url' => null,
                'tone' => 'warning',
            ];
        }

        if ($scopeLanesIncompleteCount > 0) {
            $attentionItems[] = [
                'title' => 'บางช่วงตรวจของวันนี้ยังไม่เสร็จ',
                'description' => 'ยังมีช่วงตรวจที่ยังไม่เริ่มหรือกำลังดำเนินการอยู่ จึงยังปิดงานประจำวันไม่ได้ครบถ้วน',
                'count' => $scopeLanesIncompleteCount,
                'actionLabel' => null,
                'url' => null,
                'tone' => 'info',
            ];
        }

        if ($highSeverityUnresolvedCount > 0) {
            $attentionItems[] = [
                'title' => 'มีรายงานปัญหาความรุนแรงสูงที่ต้องรีบดู',
                'description' => 'รายงานปัญหาที่ยังเปิดอยู่หรือกำลังดำเนินการและมีความรุนแรงสูงควรถูกตรวจสอบก่อน',
                'count' => $highSeverityUnresolvedCount,
                'actionLabel' => 'ดูปัญหาความรุนแรงสูง',
                'url' => $this->incidentsIndexUrl(['unresolved' => 1, 'severity' => 'High']),
                'tone' => 'danger',
            ];
        }

        if ($staleUnresolvedCount > 0) {
            $attentionItems[] = [
                'title' => 'มีรายงานปัญหาที่ยังค้างนานเกินควร',
                'description' => sprintf(
                    'รายงานปัญหาเหล่านี้ยังไม่ถูกแก้ไขมาแล้วอย่างน้อย %d วัน และควรได้รับการติดตาม',
                    IncidentStalePolicy::thresholdDays(),
                ),
                'count' => $staleUnresolvedCount,
                'actionLabel' => 'ดูปัญหาที่ค้างนาน',
                'url' => $this->incidentsIndexUrl(['unresolved' => 1, 'stale' => 1]),
                'tone' => 'warning',
            ];
        }

        if ($unownedUnresolvedCount > 0) {
            $attentionItems[] = [
                'title' => 'มีรายงานปัญหาที่ไม่มีผู้รับผิดชอบ',
                'description' => 'ยังมีรายงานปัญหาที่ยังไม่ปิดและยังไม่มีผู้รับผิดชอบ ทำให้ขั้นตอนถัดไปยังไม่ชัดว่าใครเป็นคนดูแล',
                'count' => $unownedUnresolvedCount,
                'actionLabel' => 'ดูกลุ่มปัญหาที่ไม่มีผู้รับผิดชอบ',
                'url' => $this->incidentsIndexUrl(['unowned' => 1]),
                'tone' => 'warning',
            ];
        }

        if ($overdueFollowUpCount > 0) {
            $attentionItems[] = [
                'title' => 'มีรายงานปัญหาที่เลยกำหนดติดตามแล้ว',
                'description' => 'มีอย่างน้อยหนึ่งรายงานปัญหาที่ยังไม่ปิดและเลยวันที่กำหนดติดตามแล้ว ควรตรวจสอบก่อนจะกลายเป็นงานค้างในคิว',
                'count' => $overdueFollowUpCount,
                'actionLabel' => 'ดูกลุ่มที่เลยกำหนดติดตาม',
                'url' => $this->incidentsIndexUrl(['overdue' => 1]),
                'tone' => 'danger',
            ];
        }

        return $attentionItems;
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
