<?php

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Livewire\Staff\Checklists\DailyRun;
use App\Models\ChecklistRun;
use App\Models\ChecklistRunItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = $this->createUserForRole(UserRole::Admin);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->operatorA = $this->createUserForRole(UserRole::Staff, ['email' => 'operatora@example.com']);
    $this->operatorB = $this->createUserForRole(UserRole::Staff, ['email' => 'operatorb@example.com']);
    $this->template1 = $this->createTemplateWithItems([
        'title' => 'Opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);
    $this->template2 = $this->createTemplateWithItems([
        'title' => 'Closing template',
        'scope' => ChecklistScope::CLOSING->value,
        'is_active' => false,
    ]);

    $this->createRunForUser(
        $this->operatorA,
        $this->template1,
        submitted: true,
        itemStates: $this->template1->items->map(fn () => [
            'result' => ChecklistResult::Done->value,
            'note' => null,
        ])->values()->all(),
    );
});

test('route and access audit', function () {
    $this->get('/checklists/runs/today')->assertRedirect('/login');

    $this->actingAs($this->admin)->get('/checklists/runs/today')->assertForbidden();
    $this->actingAs($this->supervisor)->get('/checklists/runs/today')->assertForbidden();
    $this->actingAs($this->operatorA)->get('/checklists/runs/today')->assertOk();
});

test('auto-create and no-duplicate proof', function () {
    $runCountBefore = ChecklistRun::where('created_by', $this->operatorB->id)
        ->where('checklist_template_id', $this->template1->id)
        ->count();
    expect($runCountBefore)->toBe(0);

    Livewire::actingAs($this->operatorB)->test(DailyRun::class);

    $runCountAfter = ChecklistRun::where('created_by', $this->operatorB->id)
        ->where('checklist_template_id', $this->template1->id)
        ->count();
    expect($runCountAfter)->toBe(1);

    Livewire::actingAs($this->operatorB)->test(DailyRun::class);

    $runCountFinal = ChecklistRun::where('created_by', $this->operatorB->id)
        ->where('checklist_template_id', $this->template1->id)
        ->count();
    expect($runCountFinal)->toBe(1);
});

test('submit validation and persistence proof', function () {
    $component = Livewire::actingAs($this->operatorB)->test(DailyRun::class);

    $component->call('submit')
        ->assertHasErrors();

    $runItems = $component->get('runItems');
    $itemIds = array_keys($runItems);

    foreach ($itemIds as $id) {
        $component->set("runItems.{$id}.result", ChecklistResult::Done->value);
        $component->set("runItems.{$id}.note", 'Tested ok');
    }

    $component->call('submit')
        ->assertHasNoErrors();

    $run = ChecklistRun::where('created_by', $this->operatorB->id)
        ->where('checklist_template_id', $this->template1->id)
        ->first();
    expect($run->submitted_at)->not->toBeNull();
    expect($run->submitted_by)->toBe($this->operatorB->id);

    foreach ($run->items as $item) {
        expect($item->result)->toBe(ChecklistResult::Done->value);
        expect($item->note)->toBe('Tested ok');
        expect($item->checked_by)->toBe($this->operatorB->id);
        expect($item->checked_at)->not->toBeNull();
    }
});

test('submitted/read-only proof', function () {
    $component = Livewire::actingAs($this->operatorA)->test(DailyRun::class);

    expect($component->get('isSubmitted'))->toBeTrue();
    $component->assertSeeHtml('disabled');

    $component->call('submit');

    $firstKey = array_key_first($component->get('runItems'));
    $component->set("runItems.{$firstKey}.result", ChecklistResult::NotDone->value);
    $component->call('submit');

    $runItem = ChecklistRunItem::find($firstKey);
    expect($runItem->result)->toBe(ChecklistResult::Done->value);
});

test('D-016 configuration error proof', function () {
    $this->template1->update(['is_active' => false]);

    Livewire::actingAs($this->operatorB)
        ->test(DailyRun::class)
        ->assertSet('errorState', 'zero')
        ->assertSee('Configuration Error')
        ->assertSee('No active checklist template exists');
});
