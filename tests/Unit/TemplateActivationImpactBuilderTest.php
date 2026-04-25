<?php

use App\Application\ChecklistTemplates\Support\TemplateActivationImpactBuilder;
use App\Models\ChecklistTemplate;
use Tests\TestCase;

uses(TestCase::class);

test('template activation impact builder explains draft live and replacement states', function () {
    $builder = app(TemplateActivationImpactBuilder::class);

    $draftImpact = $builder(
        editingTemplate: null,
        isActive: false,
        scopeLabel: 'Opening',
        currentLiveTemplate: null,
    );

    expect($draftImpact['title'])->toBe('โหมดฉบับร่าง')
        ->and($draftImpact['tone'])->toBe('info');

    $liveTemplate = new ChecklistTemplate([
        'title' => 'Current live template',
        'is_active' => false,
    ]);
    $liveTemplate->runs_count = 3;

    $replacementImpact = $builder(
        editingTemplate: null,
        isActive: true,
        scopeLabel: 'Opening',
        currentLiveTemplate: $liveTemplate,
    );

    expect($replacementImpact['title'])->toBe('การเปิดใช้งานจะยกเลิกแม่แบบที่ใช้งานจริงเดิมของรอบเวลานี้')
        ->and($replacementImpact['description'])->toContain('Current live template')
        ->and($replacementImpact['description'])->toContain('รอบ Opening')
        ->and($replacementImpact['description'])->toContain('3 รอบ')
        ->and($replacementImpact['tone'])->toBe('warning');
});
