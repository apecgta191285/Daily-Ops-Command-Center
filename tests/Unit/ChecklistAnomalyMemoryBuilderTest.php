<?php

use App\Application\Checklists\Support\ChecklistAnomalyMemoryBuilder;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Models\ChecklistItem;
use App\Models\ChecklistRun;
use App\Models\ChecklistRunItem;
use App\Models\ChecklistTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('checklist anomaly memory builder summarizes repeated not-done history by checklist item', function () {
    $user = User::factory()->staff()->create();
    $template = ChecklistTemplate::factory()->create([
        'title' => 'Opening template',
        'is_active' => true,
    ]);
    ChecklistItem::factory()->create([
        'checklist_template_id' => $template->id,
        'title' => 'Unlock main door',
        'sort_order' => 1,
    ]);
    ChecklistItem::factory()->create([
        'checklist_template_id' => $template->id,
        'title' => 'Inspect safety equipment',
        'sort_order' => 2,
    ]);
    $template->refresh()->load('items');

    $createRun = function (string $runDate, bool $submitted, array $itemStates) use ($user, $template) {
        $run = ChecklistRun::factory()->create([
            'checklist_template_id' => $template->id,
            'run_date' => $runDate,
            'assigned_team_or_scope' => $template->scope->value,
            'created_by' => $user->id,
            'submitted_at' => $submitted ? now() : null,
            'submitted_by' => $submitted ? $user->id : null,
        ]);

        foreach ($template->items->values() as $index => $item) {
            ChecklistRunItem::factory()->create([
                'checklist_run_id' => $run->id,
                'checklist_item_id' => $item->id,
                'result' => $itemStates[$index]['result'],
                'note' => $itemStates[$index]['note'],
                'checked_by' => $submitted ? $user->id : null,
                'checked_at' => $submitted ? now() : null,
            ]);
        }

        return $run;
    };

    $createRun(now()->subDays(3)->toDateString(), true, [
        ['result' => ChecklistResult::NotDone->value, 'note' => 'Door jammed'],
        ['result' => ChecklistResult::Done->value, 'note' => null],
    ]);

    $createRun(now()->subDays(2)->toDateString(), true, [
        ['result' => ChecklistResult::NotDone->value, 'note' => 'Still sticking'],
        ['result' => ChecklistResult::NotDone->value, 'note' => 'Battery low'],
    ]);

    $currentRun = $createRun(today()->toDateString(), false, [
        ['result' => null, 'note' => null],
        ['result' => null, 'note' => null],
    ]);

    $memory = app(ChecklistAnomalyMemoryBuilder::class)->forUserAndTemplate(
        userId: $user->id,
        templateId: $template->id,
        excludeRunId: $currentRun->id,
    );

    $firstItemId = $template->items[0]->id;
    $secondItemId = $template->items[1]->id;

    expect($memory[$firstItemId]['recent_not_done_count'])->toBe(2)
        ->and($memory[$firstItemId]['sample_run_count'])->toBe(2)
        ->and($memory[$firstItemId]['last_note'])->toBe('Still sticking')
        ->and($memory[$secondItemId]['recent_not_done_count'])->toBe(1)
        ->and($memory[$secondItemId]['last_note'])->toBe('Battery low');
});
