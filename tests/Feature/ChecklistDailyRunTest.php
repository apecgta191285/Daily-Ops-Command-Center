<?php

use App\Livewire\Staff\Checklists\DailyRun;
use App\Models\ChecklistRun;
use App\Models\ChecklistRunItem;
use App\Models\ChecklistTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(); // Seed the baseline data
    $this->admin = User::where('role', 'admin')->first();
    $this->supervisor = User::where('role', 'supervisor')->first();
    $this->operatorA = User::where('email', 'operatora@example.com')->first();
    $this->operatorB = User::where('email', 'operatorb@example.com')->first();
    $this->template1 = ChecklistTemplate::where('title', 'เปิดห้องปฏิบัติการ')->first();
    $this->template2 = ChecklistTemplate::where('title', 'ปิดห้องปฏิบัติการ')->first();
});

test('route and access audit', function () {
    // Unauthenticated -> redirect to login
    $this->get('/checklists/runs/today')->assertRedirect('/login');

    // Admin -> 403 Forbidden
    $this->actingAs($this->admin)->get('/checklists/runs/today')->assertForbidden();

    // Supervisor -> 403 Forbidden
    $this->actingAs($this->supervisor)->get('/checklists/runs/today')->assertForbidden();

    // Staff -> 200 OK
    $this->actingAs($this->operatorA)->get('/checklists/runs/today')->assertOk();
});

test('auto-create and no-duplicate proof', function () {
    // Operator B has NO run for template 1 today
    $runCountBefore = ChecklistRun::where('created_by', $this->operatorB->id)
        ->where('checklist_template_id', $this->template1->id)
        ->count();
    expect($runCountBefore)->toBe(0);

    // Initial visit should create the run
    Livewire::actingAs($this->operatorB)->test(DailyRun::class);

    $runCountAfter = ChecklistRun::where('created_by', $this->operatorB->id)
        ->where('checklist_template_id', $this->template1->id)
        ->count();
    expect($runCountAfter)->toBe(1);

    // Visit again, should not create duplicate
    Livewire::actingAs($this->operatorB)->test(DailyRun::class);

    $runCountFinal = ChecklistRun::where('created_by', $this->operatorB->id)
        ->where('checklist_template_id', $this->template1->id)
        ->count();
    expect($runCountFinal)->toBe(1);
});

test('submit validation and persistence proof', function () {
    // Operator B initial load (auto-creates)
    $component = Livewire::actingAs($this->operatorB)->test(DailyRun::class);

    // Try submitting without answers
    $component->call('submit')
        ->assertHasErrors();

    // Get the run items directly from component state
    $runItems = $component->get('runItems');
    $itemIds = array_keys($runItems);

    // Fill all items with 'Done' and optional note
    foreach ($itemIds as $id) {
        $component->set("runItems.{$id}.result", 'Done');
        $component->set("runItems.{$id}.note", 'Tested ok');
    }

    $component->call('submit')
        ->assertHasNoErrors();

    // Assert database changes
    $run = ChecklistRun::where('created_by', $this->operatorB->id)
        ->where('checklist_template_id', $this->template1->id)
        ->first();
    expect($run->submitted_at)->not->toBeNull();
    expect($run->submitted_by)->toBe($this->operatorB->id);

    foreach ($run->items as $item) {
        expect($item->result)->toBe('Done');
        expect($item->note)->toBe('Tested ok');
        expect($item->checked_by)->toBe($this->operatorB->id);
        expect($item->checked_at)->not->toBeNull();
    }
});

test('submitted/read-only proof', function () {
    // Operator A's run is already submitted via Seeder
    $component = Livewire::actingAs($this->operatorA)->test(DailyRun::class);

    // Verify it is submitted
    expect($component->get('isSubmitted'))->toBeTrue();

    // Assert form is disabled visually
    $component->assertSeeHtml('disabled');

    // Trying to submit again should return early
    $component->call('submit');

    // Check if we can alter data after submission (the component shouldn't let it save)
    $firstKey = array_key_first($component->get('runItems'));
    $component->set("runItems.{$firstKey}.result", 'Not Done');
    $component->call('submit');

    // DB should remain 'Done' mapping exactly the Seeder for Operator A
    $runItem = ChecklistRunItem::find($firstKey);
    expect($runItem->result)->toBe('Done');
});

test('D-016 configuration error proof', function () {
    // Zero active templates
    $this->template1->update(['is_active' => false]);

    Livewire::actingAs($this->operatorB)
        ->test(DailyRun::class)
        ->assertSet('errorState', 'zero')
        ->assertSee('Configuration Error')
        ->assertSee('No active checklist template exists');

    // Multiple active templates
    $this->template1->update(['is_active' => true]);
    $this->template2->update(['is_active' => true]);

    Livewire::actingAs($this->operatorB)
        ->test(DailyRun::class)
        ->assertSet('errorState', 'multiple')
        ->assertSee('Configuration Error')
        ->assertSee('Multiple active checklist templates');
});
