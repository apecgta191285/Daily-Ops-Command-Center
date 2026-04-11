<?php

use App\Application\Checklists\Actions\InitializeDailyRun;
use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->operator = $this->createUserForRole(UserRole::Staff);
    $this->activeTemplate = $this->createTemplateWithItems([
        'title' => 'Active opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);
});

test('initialize daily run creates exactly one run for the active template', function () {
    $action = app(InitializeDailyRun::class);

    $context = $action($this->operator->id);

    expect($context->errorState)->toBeNull();
    expect($context->run)->not->toBeNull();
    expect($context->template)->not->toBeNull();
    expect($context->isSubmitted)->toBeFalse();
    expect($context->runItems)->not->toBeEmpty();

    $count = ChecklistRun::query()
        ->where('created_by', $this->operator->id)
        ->where('checklist_template_id', $context->template->id)
        ->count();

    expect($count)->toBe(1);

    $action($this->operator->id);

    $countAfterSecondCall = ChecklistRun::query()
        ->where('created_by', $this->operator->id)
        ->where('checklist_template_id', $context->template->id)
        ->count();

    expect($countAfterSecondCall)->toBe(1);
});

test('initialize daily run returns zero error when no active template exists', function () {
    $this->activeTemplate->update(['is_active' => false]);

    $context = app(InitializeDailyRun::class)($this->operator->id);

    expect($context->errorState)->toBe('zero');
    expect($context->run)->toBeNull();
});

test('persistence invariant prevents creating multiple active templates in normal execution', function () {
    expect(fn () => ChecklistTemplate::query()->create([
        'title' => 'อีกหนึ่งเทมเพลต active',
        'description' => 'ต้องถูกปฏิเสธโดยฐานข้อมูล',
        'scope' => ChecklistScope::MIDDAY->value,
        'is_active' => true,
    ]))->toThrow(QueryException::class);
});
