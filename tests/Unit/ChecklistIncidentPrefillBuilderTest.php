<?php

use App\Application\Checklists\Support\ChecklistIncidentPrefillBuilder;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Models\ChecklistRun;
use App\Models\ChecklistRunItem;
use App\Models\ChecklistTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('checklist incident prefill builder creates follow-up context from a daily run', function () {
    $user = User::factory()->create();
    $template = ChecklistTemplate::factory()->create(['title' => 'Opening Duties']);
    $run = ChecklistRun::factory()
        ->for($template, 'template')
        ->for($user, 'creator')
        ->create([
            'run_date' => '2026-04-17',
        ]);

    $firstItem = ChecklistRunItem::factory()
        ->for($run, 'run')
        ->create();
    $secondItem = ChecklistRunItem::factory()
        ->for($run, 'run')
        ->create();

    $run->load('items.checklistItem');

    $prefill = app(ChecklistIncidentPrefillBuilder::class)->fromDailyRun($run, $template, [
        $firstItem->id => ['result' => ChecklistResult::NotDone->value, 'note' => 'Door jammed'],
        $secondItem->id => ['result' => ChecklistResult::Done->value, 'note' => null],
    ]);

    expect($prefill->title)->toBe('Checklist follow-up issue')
        ->and($prefill->category)->toBe(IncidentCategory::Other->value)
        ->and($prefill->severity)->toBe(IncidentSeverity::Medium->value)
        ->and($prefill->description)->toContain('Follow-up from the daily checklist.')
        ->and($prefill->description)->toContain('Template: Opening Duties')
        ->and($prefill->description)->toContain('Run date: 2026-04-17')
        ->and($prefill->description)->toContain($firstItem->checklistItem->title);
});
