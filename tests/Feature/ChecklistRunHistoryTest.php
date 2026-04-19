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

test('historical run recap shows grouped responses and hides unsubmitted runs', function () {
    $submittedRun = $this->createRunForUser(
        $this->operatorA,
        $this->openingTemplate,
        submitted: true,
        itemStates: [
            ['result' => ChecklistResult::Done->value, 'note' => null],
            ['result' => ChecklistResult::NotDone->value, 'note' => 'Projector lamp issue'],
        ],
        runDate: '2026-04-17',
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
    $response->assertSee('Opening checks');
    $response->assertSee('Unlock room');
    $response->assertSee('Check projector');
    $response->assertSee('Projector lamp issue');
    $response->assertSee('Follow-up worth reviewing');
    $response->assertSee('Review same day');
    $response->assertSee('Review same scope');
    $response->assertSee('Review same operator');

    $this->actingAs($this->supervisor)
        ->get(route('checklists.history.show', $draftRun))
        ->assertNotFound();
});
