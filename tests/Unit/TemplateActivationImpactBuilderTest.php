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
        currentLiveTemplate: null,
    );

    expect($draftImpact['title'])->toBe('Draft mode')
        ->and($draftImpact['tone'])->toBe('info');

    $liveTemplate = new ChecklistTemplate([
        'title' => 'Current live template',
        'is_active' => false,
    ]);
    $liveTemplate->runs_count = 3;

    $replacementImpact = $builder(
        editingTemplate: null,
        isActive: true,
        currentLiveTemplate: $liveTemplate,
    );

    expect($replacementImpact['title'])->toBe('Activation will retire the current live template')
        ->and($replacementImpact['description'])->toContain('Current live template')
        ->and($replacementImpact['description'])->toContain('3 recorded run(s)')
        ->and($replacementImpact['tone'])->toBe('warning');
});
