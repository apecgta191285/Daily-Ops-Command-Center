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
    $this->room = $this->createRoom(['name' => 'Lab 1', 'code' => 'LAB-01']);
    $this->template1 = $this->createTemplateWithItems([
        'title' => 'Opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ], [
        ['title' => 'Unlock main door', 'description' => 'Open the room for the day', 'group_label' => 'Opening checks'],
        ['title' => 'Inspect safety equipment', 'description' => 'Verify extinguisher and exits', 'group_label' => 'Safety checks'],
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
        room: $this->room,
    );
});

test('route and access audit', function () {
    $this->get('/checklists/runs/today')->assertRedirect('/login');

    $this->actingAs($this->admin)->get('/checklists/runs/today')->assertForbidden();
    $this->actingAs($this->supervisor)->get('/checklists/runs/today')->assertForbidden();
    $this->actingAs($this->operatorA)->get('/checklists/runs/today')->assertOk();
});

test('daily checklist shows a runtime board when multiple scope templates are live', function () {
    $this->template2->update(['is_active' => true]);

    Livewire::actingAs($this->operatorB)
        ->withQueryParams(['room' => $this->room->id])
        ->test(DailyRun::class)
        ->assertSet('errorState', 'scope_required')
        ->assertSee('Choose the checklist lane for this room')
        ->assertSee('Opening')
        ->assertSee('Closing')
        ->assertSee('Enter lane');
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

test('daily checklist progress summary updates as responses are filled in', function () {
    $component = Livewire::actingAs($this->operatorB)->test(DailyRun::class);

    expect($component->get('totalItems'))->toBe(2);
    expect($component->get('answeredItems'))->toBe(0);
    expect($component->get('remainingItems'))->toBe(2);
    expect($component->get('completionPercentage'))->toBe(0);

    $runItems = $component->get('runItems');
    $itemIds = array_keys($runItems);

    $component->set("runItems.{$itemIds[0]}.result", ChecklistResult::Done->value);

    expect($component->get('answeredItems'))->toBe(1);
    expect($component->get('remainingItems'))->toBe(1);
    expect($component->get('completionPercentage'))->toBe(50);

    $component->set("runItems.{$itemIds[1]}.result", ChecklistResult::NotDone->value);

    expect($component->get('answeredItems'))->toBe(2);
    expect($component->get('remainingItems'))->toBe(0);
    expect($component->get('notDoneItems'))->toBe(1);
    expect($component->get('completionPercentage'))->toBe(100);
});

test('daily checklist renders lightweight group headings from template item labels', function () {
    Livewire::actingAs($this->operatorB)
        ->test(DailyRun::class)
        ->assertSee('Opening checks')
        ->assertSee('Safety checks')
        ->assertSee('Unlock main door')
        ->assertSee('Inspect safety equipment');
});

test('daily checklist shows recent submission context for the current operator', function () {
    $this->createRunForUser(
        $this->operatorB,
        $this->template1,
        submitted: true,
        itemStates: [
            ['result' => ChecklistResult::Done->value, 'note' => 'Ready'],
            ['result' => ChecklistResult::NotDone->value, 'note' => 'Printer issue'],
        ],
        runDate: now()->subDay()->toDateString(),
        room: $this->room,
    );

    Livewire::actingAs($this->operatorB)
        ->test(DailyRun::class)
        ->assertSee('Recent Submission Context')
        ->assertSee('1 not done')
        ->assertSee('2 note(s)');
});

test('daily checklist can load a selected scope lane directly from the route key', function () {
    $this->template2->update(['is_active' => true]);

    Livewire::actingAs($this->operatorB)
        ->test(DailyRun::class, ['scope' => ChecklistScope::CLOSING->routeKey()])
        ->assertSet('errorState', null)
        ->assertSet('scopeRouteKey', ChecklistScope::CLOSING->routeKey())
        ->assertSee($this->template2->title);
});

test('daily checklist shows lightweight anomaly memory for items with recent not-done history', function () {
    $this->createRunForUser(
        $this->operatorB,
        $this->template1,
        submitted: true,
        itemStates: [
            ['result' => ChecklistResult::NotDone->value, 'note' => 'Door jammed'],
            ['result' => ChecklistResult::Done->value, 'note' => null],
        ],
        runDate: now()->subDay()->toDateString(),
        room: $this->room,
    );

    Livewire::actingAs($this->operatorB)
        ->test(DailyRun::class)
        ->assertSee('Recent issue memory:')
        ->assertSee('marked Not Done 1 time(s)')
        ->assertSee('Door jammed');
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

test('daily checklist blocks room-tied execution when no active room exists', function () {
    $this->room->update(['is_active' => false]);

    Livewire::actingAs($this->operatorB)
        ->test(DailyRun::class)
        ->assertSet('errorState', 'room_missing')
        ->assertSee('Room setup required')
        ->assertSee('There are no active rooms available yet');

    expect(ChecklistRun::query()
        ->where('created_by', $this->operatorB->id)
        ->where('checklist_template_id', $this->template1->id)
        ->exists())->toBeFalse();
});

test('submission success feedback reflects not-done answers', function () {
    $component = Livewire::actingAs($this->operatorB)->test(DailyRun::class);

    foreach (array_keys($component->get('runItems')) as $index => $id) {
        $component->set(
            "runItems.{$id}.result",
            $index === 0 ? ChecklistResult::NotDone->value : ChecklistResult::Done->value
        );
    }

    $component->call('submit')
        ->assertHasNoErrors()
        ->assertSee('Checklist submitted successfully. 1 item(s) were marked Not Done.');
});

test('submitted checklist recap offers a follow-up incident shortcut when items are not done', function () {
    $this->createRunForUser(
        $this->operatorB,
        $this->template1,
        submitted: true,
        itemStates: [
            ['result' => ChecklistResult::NotDone->value, 'note' => 'Printer offline'],
            ['result' => ChecklistResult::Done->value, 'note' => null],
        ],
        room: $this->room,
    );

    $component = Livewire::actingAs($this->operatorB)->test(DailyRun::class);

    expect($component->get('notDoneItems'))->toBe(1);
    expect($component->get('notedItems'))->toBe(1);

    $prefillUrl = $component->get('incidentPrefillUrl');

    expect($prefillUrl)->toContain('/incidents/new');
    expect(urldecode($prefillUrl))->toContain('Checklist follow-up issue');
    expect(urldecode($prefillUrl))->toContain('Unlock main door');
    expect($prefillUrl)->toContain('checklist_scope=opening');

    $component
        ->assertSee('Submission Recap')
        ->assertSee('Report follow-up incident');
});

test('submission recap highlights repeated not-done items when history exists', function () {
    $this->createRunForUser(
        $this->operatorB,
        $this->template1,
        submitted: true,
        itemStates: [
            ['result' => ChecklistResult::NotDone->value, 'note' => 'Printer offline'],
            ['result' => ChecklistResult::Done->value, 'note' => null],
        ],
        runDate: now()->subDay()->toDateString(),
    );

    $component = Livewire::actingAs($this->operatorB)->test(DailyRun::class);

    foreach (array_keys($component->get('runItems')) as $index => $id) {
        $component->set(
            "runItems.{$id}.result",
            $index === 0 ? ChecklistResult::NotDone->value : ChecklistResult::Done->value
        );
    }

    $component->call('submit')
        ->assertHasNoErrors()
        ->assertSee('Repeated issue memory:')
        ->assertSee('Unlock main door');
});
