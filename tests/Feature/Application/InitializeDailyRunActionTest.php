<?php

use App\Application\Checklists\Actions\InitializeDailyRun;
use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->operator = User::where('email', 'operatorb@example.com')->firstOrFail();
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
    ChecklistTemplate::query()->update(['is_active' => false]);

    $context = app(InitializeDailyRun::class)($this->operator->id);

    expect($context->errorState)->toBe('zero');
    expect($context->run)->toBeNull();
});

test('initialize daily run returns multiple error when more than one active template exists', function () {
    ChecklistTemplate::query()->update(['is_active' => true]);

    $context = app(InitializeDailyRun::class)($this->operator->id);

    expect($context->errorState)->toBe('multiple');
    expect($context->run)->toBeNull();
});
