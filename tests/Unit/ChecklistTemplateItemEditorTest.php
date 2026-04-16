<?php

use App\Application\ChecklistTemplates\Support\ChecklistTemplateItemEditor;
use Tests\TestCase;

uses(TestCase::class);

test('checklist template item editor adds and reindexes editor items safely', function () {
    $editor = app(ChecklistTemplateItemEditor::class);

    $items = $editor->add([]);
    $items = $editor->add($items);
    $items[0]['title'] = 'First';
    $items[1]['title'] = 'Second';

    $items = $editor->remove($items, 0);

    expect($items)->toHaveCount(1)
        ->and($items[0]['title'])->toBe('Second')
        ->and($items[0]['sort_order'])->toBe(1);
});
