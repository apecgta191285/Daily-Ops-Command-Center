<?php

declare(strict_types=1);

namespace App\Application\ChecklistTemplates\Support;

use App\Models\ChecklistTemplate;

class TemplateActivationImpactBuilder
{
    /**
     * @return array{
     *     title: string,
     *     description: string,
     *     tone: 'info'|'warning'
     * }
     */
    public function __invoke(
        ?ChecklistTemplate $editingTemplate,
        bool $isActive,
        string $scopeLabel,
        ?ChecklistTemplate $currentLiveTemplate,
    ): array {
        if (! $isActive) {
            return [
                'title' => 'โหมดฉบับร่าง',
                'description' => "แม่แบบนี้จะยังไม่เปิดใช้งานหลังบันทึก ใช้กรณีที่ต้องการเตรียมฉบับแก้ไขของรอบ {$scopeLabel} โดยยังไม่เปลี่ยนรายการตรวจเช็กที่ใช้งานจริง",
                'tone' => 'info',
            ];
        }

        if ($editingTemplate?->exists && $editingTemplate->is_active) {
            return [
                'title' => 'แม่แบบที่ใช้งานจริงของรอบเวลานี้ยังคงเดิม',
                'description' => "แม่แบบนี้เป็นรายการตรวจเช็กที่ใช้งานจริงของรอบ {$scopeLabel} อยู่แล้ว การบันทึกจะคงสถานะใช้งานและอัปเดตเวอร์ชันที่ใช้จริงของรอบนี้โดยตรง",
                'tone' => 'info',
            ];
        }

        if ($currentLiveTemplate) {
            $historyNote = $currentLiveTemplate->runs_count > 0
                ? " แม่แบบที่ใช้งานจริงปัจจุบันมีประวัติรอบการตรวจเช็กอยู่แล้ว {$currentLiveTemplate->runs_count} รอบ"
                : '';

            return [
                'title' => 'การเปิดใช้งานจะยกเลิกแม่แบบที่ใช้งานจริงเดิมของรอบเวลานี้',
                'description' => "หากบันทึกแม่แบบนี้เป็นแบบใช้งานจริง จะยกเลิก \"{$currentLiveTemplate->title}\" จากการใช้งานจริงของรอบ {$scopeLabel} และแทนที่ทันที{$historyNote}",
                'tone' => 'warning',
            ];
        }

        return [
            'title' => 'นี่จะเป็นแม่แบบใช้งานจริงชุดแรกของรอบเวลานี้',
            'description' => "ขณะนี้ยังไม่มีแม่แบบ {$scopeLabel} ที่เปิดใช้งานอยู่ ดังนั้นเมื่อบันทึกแม่แบบนี้เป็นแบบใช้งานจริง ระบบจะใช้แม่แบบนี้เป็นรายการตรวจเช็กหลักของรอบนี้",
            'tone' => 'info',
        ];
    }
}
