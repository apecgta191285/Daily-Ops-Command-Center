<?php

use App\Application\Checklists\Actions\InitializeDailyRun;
use App\Application\Checklists\Actions\SubmitDailyRun;
use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->operator = $this->createUserForRole(UserRole::Staff);
    $this->createTemplateWithItems([
        'title' => 'Submit action template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);
    $this->context = app(InitializeDailyRun::class)($this->operator->id);
});

test('submit daily run persists answers and submit metadata', function () {
    $payload = collect($this->context->runItems)
        ->map(fn () => ['result' => 'Done', 'note' => 'Checked in action test'])
        ->all();

    $run = app(SubmitDailyRun::class)($this->context->run, $payload, $this->operator->id);

    expect($run->submitted_at)->not->toBeNull();
    expect($run->submitted_by)->toBe($this->operator->id);

    foreach ($run->items as $item) {
        expect($item->result)->toBe('Done');
        expect($item->note)->toBe('Checked in action test');
        expect($item->checked_by)->toBe($this->operator->id);
        expect($item->checked_at)->not->toBeNull();
    }
});

test('submit daily run rejects invalid result values', function () {
    $payload = $this->context->runItems;
    $firstKey = array_key_first($payload);
    $payload[$firstKey]['result'] = 'Broken';

    expect(fn () => app(SubmitDailyRun::class)($this->context->run, $payload, $this->operator->id))
        ->toThrow(ValidationException::class);
});
