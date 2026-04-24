<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Checklists\Enums\ChecklistScope;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = $this->createUserForRole(UserRole::Admin);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->staff = $this->createUserForRole(UserRole::Staff);
    $this->operatorA = $this->createUserForRole(UserRole::Staff, ['name' => 'Operator Archive A', 'email' => 'archivea@example.com']);
    $this->operatorB = $this->createUserForRole(UserRole::Staff, ['name' => 'Operator Archive B', 'email' => 'archiveb@example.com']);

    $this->openingTemplate = $this->createTemplateWithItems([
        'title' => 'Opening archive template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ], [
        ['title' => 'Unlock room', 'description' => 'Prepare opening', 'group_label' => 'Opening checks'],
        ['title' => 'Check projector', 'description' => 'Verify display', 'group_label' => 'Opening checks'],
    ]);

    $this->closingTemplate = $this->createTemplateWithItems([
        'title' => 'Closing archive template',
        'scope' => ChecklistScope::CLOSING->value,
        'is_active' => false,
    ], [
        ['title' => 'Power down PCs', 'description' => 'Shut systems down', 'group_label' => 'Closing checks'],
    ]);
});

test('management-only route access applies to checklist run archive', function () {
    $this->get('/checklists/history')->assertRedirect('/login');

    $this->actingAs($this->admin)->get('/checklists/history')->assertOk();
    $this->actingAs($this->supervisor)->get('/checklists/history')->assertOk();
    $this->actingAs($this->staff)->get('/checklists/history')->assertForbidden();
});

test('management-only route access applies to printable checklist run recap', function () {
    $run = $this->createRunForUser(
        $this->operatorA,
        $this->openingTemplate,
        submitted: true,
        runDate: '2026-04-17',
    );

    $this->get(route('checklists.history.print', $run))->assertRedirect('/login');

    $this->actingAs($this->admin)->get(route('checklists.history.print', $run))->assertOk();
    $this->actingAs($this->supervisor)->get(route('checklists.history.print', $run))->assertOk();
    $this->actingAs($this->staff)->get(route('checklists.history.print', $run))->assertForbidden();
});

test('checklist run archive lists only submitted runs and respects date scope and operator filters', function () {
    $openingRun = $this->createRunForUser(
        $this->operatorA,
        $this->openingTemplate,
        submitted: true,
        itemStates: [
            ['result' => ChecklistResult::Done->value, 'note' => null],
            ['result' => ChecklistResult::NotDone->value, 'note' => 'Projector lamp issue'],
        ],
        runDate: '2026-04-17',
    );

    $closingRun = $this->createRunForUser(
        $this->operatorB,
        $this->closingTemplate,
        submitted: true,
        itemStates: [
            ['result' => ChecklistResult::Done->value, 'note' => 'Closed cleanly'],
        ],
        runDate: '2026-04-18',
    );

    $this->createRunForUser(
        $this->operatorB,
        $this->openingTemplate,
        submitted: false,
        itemStates: [
            ['result' => ChecklistResult::Done->value, 'note' => null],
            ['result' => null, 'note' => null],
        ],
        runDate: '2026-04-19',
    );

    $response = $this->actingAs($this->admin)->get('/checklists/history');

    $response->assertOk();
    $response->assertSee('Checklist Run Archive');
    $response->assertSee('Archive day context');
    $response->assertSee('Opening');
    $response->assertSee('Closing');
    $response->assertSee($this->openingTemplate->title);
    $response->assertSee($this->closingTemplate->title);
    $response->assertSee($this->operatorA->name);
    $response->assertSee($this->operatorB->name);
    $response->assertDontSee('2026-04-19');

    $this->actingAs($this->admin)
        ->get('/checklists/history?scope=opening')
        ->assertSee($this->openingTemplate->title)
        ->assertDontSee($this->closingTemplate->title);

    $this->actingAs($this->admin)
        ->get('/checklists/history?operator='.$this->operatorB->id)
        ->assertSee($this->closingTemplate->title)
        ->assertDontSee($this->openingTemplate->title);

    $this->actingAs($this->admin)
        ->get('/checklists/history?runDate=2026-04-17')
        ->assertSee($this->openingTemplate->title)
        ->assertDontSee($this->closingTemplate->title);
});

test('checklist run archive paginates long filtered result sets without losing archive context', function () {
    $firstRoom = null;
    $lastRoom = null;

    foreach (range(1, 18) as $index) {
        $room = $this->createRoom([
            'name' => "Archive Pagination Lab {$index}",
            'code' => sprintf('LAB-AP-%02d', $index),
        ]);

        $firstRoom ??= $room;
        $lastRoom = $room;

        $this->createRunForUser(
            $this->operatorA,
            $this->openingTemplate,
            submitted: true,
            runDate: '2026-04-17',
            room: $room,
        );
    }

    $this->createRunForUser(
        $this->operatorB,
        $this->closingTemplate,
        submitted: true,
        runDate: '2026-04-18',
    );

    $response = $this->actingAs($this->admin)->get('/checklists/history?runDate=2026-04-17');

    $response->assertOk();
    $response->assertSee('Archive day context');
    $response->assertSee('Opening archive template');
    $response->assertDontSee('Closing archive template');
    $response->assertSee($lastRoom->name);

    $pageTwoResponse = $this->actingAs($this->admin)->get('/checklists/history?runDate=2026-04-17&page=2');

    $pageTwoResponse->assertOk();
    $pageTwoResponse->assertSee('Archive day context');
    $pageTwoResponse->assertSee('Opening archive template');
    $pageTwoResponse->assertSee($firstRoom->name);
    $pageTwoResponse->assertDontSee($lastRoom->name);
    $pageTwoResponse->assertDontSee('Closing archive template');
});

test('checklist run archive ignores malformed runDate values instead of crashing', function () {
    $this->createRunForUser(
        $this->operatorA,
        $this->openingTemplate,
        submitted: true,
        runDate: '2026-04-17',
    );

    $this->createRunForUser(
        $this->operatorB,
        $this->closingTemplate,
        submitted: true,
        runDate: '2026-04-18',
    );

    $response = $this->actingAs($this->admin)->get('/checklists/history?runDate=not-a-date');

    $response->assertOk();
    $response->assertSee($this->openingTemplate->title);
    $response->assertSee($this->closingTemplate->title);
});

test('historical run recap shows grouped responses and hides unsubmitted runs', function () {
    $room = $this->createRoom([
        'name' => 'Lab Archive 1',
        'code' => 'LAB-A1',
    ]);

    $submittedRun = $this->createRunForUser(
        $this->operatorA,
        $this->openingTemplate,
        submitted: true,
        itemStates: [
            ['result' => ChecklistResult::Done->value, 'note' => null],
            ['result' => ChecklistResult::NotDone->value, 'note' => 'Projector lamp issue'],
        ],
        runDate: '2026-04-17',
        room: $room,
    );

    $draftRun = $this->createRunForUser(
        $this->operatorB,
        $this->openingTemplate,
        submitted: false,
    );

    $response = $this->actingAs($this->supervisor)->get(route('checklists.history.show', $submittedRun));

    $response->assertOk();
    $response->assertSee('Historical recap');
    $response->assertSee($this->openingTemplate->title);
    $response->assertSee($room->name);
    $response->assertSee('Opening checks');
    $response->assertSee('Unlock room');
    $response->assertSee('Check projector');
    $response->assertSee('Projector lamp issue');
    $response->assertSee('Follow-up worth reviewing');
    $response->assertSee('Review same day');
    $response->assertSee('Review same scope');
    $response->assertSee('Review same operator');
    $response->assertSee('Printable recap');

    $this->actingAs($this->supervisor)
        ->get(route('checklists.history.show', $draftRun))
        ->assertNotFound();
});

test('printable historical run recap shows print-friendly evidence summary', function () {
    $room = $this->createRoom([
        'name' => 'Lab Print 1',
        'code' => 'LAB-P1',
    ]);

    $submittedRun = $this->createRunForUser(
        $this->operatorA,
        $this->openingTemplate,
        submitted: true,
        itemStates: [
            ['result' => ChecklistResult::Done->value, 'note' => null],
            ['result' => ChecklistResult::NotDone->value, 'note' => 'Projector lamp issue'],
        ],
        runDate: '2026-04-17',
        room: $room,
    );

    $response = $this->actingAs($this->admin)->get(route('checklists.history.print', $submittedRun));

    $response->assertOk();
    $response->assertSee('Checklist recap print view');
    $response->assertSee('Print recap');
    $response->assertSee($this->openingTemplate->title);
    $response->assertSee($room->name);
    $response->assertSee('Evidence snapshot');
    $response->assertSee('Follow-up worth reviewing');
    $response->assertSee('Projector lamp issue');
});
