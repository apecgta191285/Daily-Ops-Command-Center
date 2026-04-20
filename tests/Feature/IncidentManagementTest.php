<?php

use App\Domain\Access\Enums\UserRole;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Livewire\Management\Incidents\Index;
use App\Livewire\Management\Incidents\Show;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->admin = User::where('role', UserRole::Admin->value)->first();
    $this->supervisor = User::where('role', UserRole::Supervisor->value)->first();
    $this->staff = User::where('role', UserRole::Staff->value)->first();
    $this->openIncident = Incident::where('status', 'Open')->firstOrFail();
    $this->inProgressIncident = Incident::where('status', 'In Progress')->firstOrFail();
    $this->resolvedIncident = Incident::where('status', 'Resolved')->firstOrFail();
});

test('incident management routes are restricted to management users', function () {
    $this->get(route('incidents.index'))->assertRedirect(route('login'));
    $this->get(route('incidents.show', $this->openIncident))->assertRedirect(route('login'));

    $this->actingAs($this->staff)->get(route('incidents.index'))->assertForbidden();
    $this->actingAs($this->staff)->get(route('incidents.show', $this->openIncident))->assertForbidden();

    $this->actingAs($this->admin)->get(route('incidents.index'))->assertOk();
    $this->actingAs($this->supervisor)->get(route('incidents.index'))->assertOk();

    $this->actingAs($this->admin)->get(route('incidents.show', $this->openIncident))->assertOk();
    $this->actingAs($this->supervisor)->get(route('incidents.show', $this->openIncident))->assertOk();
});

test('management users can filter the incident list', function () {
    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('status', 'Resolved')
        ->assertSee($this->resolvedIncident->title)
        ->assertDontSee($this->openIncident->title);

    $networkIncident = Incident::where('category', 'เครือข่าย')->firstOrFail();
    $cleanlinessIncident = Incident::where('category', 'ความสะอาด')->firstOrFail();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('category', 'เครือข่าย')
        ->assertSee($networkIncident->title)
        ->assertDontSee($cleanlinessIncident->title);

    $highSeverityIncident = Incident::where('severity', 'High')->firstOrFail();
    $lowSeverityIncident = Incident::where('severity', 'Low')->firstOrFail();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->set('severity', 'High')
        ->assertSee($highSeverityIncident->title)
        ->assertDontSee($lowSeverityIncident->title);
});

test('incident list paginates long queues without dropping filter behavior', function () {
    Incident::query()->delete();

    foreach (range(1, 18) as $index) {
        $incident = Incident::create([
            'title' => sprintf('Queue incident %02d', $index),
            'category' => 'อื่น ๆ',
            'severity' => 'Low',
            'status' => 'Open',
            'description' => 'Queue pagination proof.',
            'created_by' => $this->admin->id,
        ]);

        $incident->forceFill([
            'created_at' => now()->subMinutes(18 - $index),
            'updated_at' => now()->subMinutes(18 - $index),
        ])->saveQuietly();
    }

    $this->actingAs($this->admin)
        ->get(route('incidents.index'))
        ->assertOk()
        ->assertSee('Queue incident 18')
        ->assertDontSee('Queue incident 01');

    $this->actingAs($this->admin)
        ->get(route('incidents.index', ['page' => 2]))
        ->assertOk()
        ->assertSee('Queue incident 03')
        ->assertSee('Queue incident 01')
        ->assertDontSee('Queue incident 18');
});

test('incident list honors unresolved and stale query-driven filters', function () {
    $freshOpenIncident = Incident::create([
        'title' => 'Fresh open incident',
        'category' => 'อื่น ๆ',
        'severity' => 'Low',
        'status' => 'Open',
        'description' => 'Fresh unresolved incident.',
        'created_by' => $this->admin->id,
    ]);

    $staleOpenIncident = Incident::create([
        'title' => 'Stale open incident',
        'category' => 'เครือข่าย',
        'severity' => 'High',
        'status' => 'Open',
        'description' => 'Old unresolved incident.',
        'created_by' => $this->admin->id,
    ]);

    $staleOpenIncident->forceFill([
        'created_at' => now()->subDays(3),
        'updated_at' => now()->subDays(3),
    ])->saveQuietly();

    $resolvedIncident = Incident::create([
        'title' => 'Resolved incident for filter proof',
        'category' => 'อื่น ๆ',
        'severity' => 'Medium',
        'status' => 'Resolved',
        'description' => 'Resolved incident should be excluded by unresolved filter.',
        'created_by' => $this->admin->id,
        'resolved_at' => now(),
    ]);

    $resolvedIncident->activities()->create([
        'action_type' => 'status_changed',
        'summary' => 'Status changed to Resolved',
        'actor_id' => $this->admin->id,
        'created_at' => now(),
    ]);

    $this->actingAs($this->admin)
        ->get(route('incidents.index', ['unresolved' => 1]))
        ->assertOk()
        ->assertSee($freshOpenIncident->title)
        ->assertSee($staleOpenIncident->title)
        ->assertDontSee($resolvedIncident->title);

    $this->actingAs($this->admin)
        ->get(route('incidents.index', ['unresolved' => 1, 'stale' => 1]))
        ->assertOk()
        ->assertSee($staleOpenIncident->title)
        ->assertDontSee($freshOpenIncident->title)
        ->assertDontSee($resolvedIncident->title)
        ->assertSee('Stale');
});

test('incident list honors accountability query-driven filters', function () {
    $ownedByAdmin = Incident::create([
        'title' => 'Owned by admin in queue',
        'category' => 'อื่น ๆ',
        'severity' => 'Medium',
        'status' => 'In Progress',
        'description' => 'Owned queue incident.',
        'created_by' => $this->staff->id,
        'owner_id' => $this->admin->id,
        'follow_up_due_at' => today()->addDay(),
    ]);

    $unownedIncident = Incident::create([
        'title' => 'Unowned queue incident',
        'category' => 'เครือข่าย',
        'severity' => 'High',
        'status' => 'Open',
        'description' => 'Needs an owner.',
        'created_by' => $this->staff->id,
    ]);

    $overdueIncident = Incident::create([
        'title' => 'Overdue queue incident',
        'category' => 'ความปลอดภัย',
        'severity' => 'High',
        'status' => 'Open',
        'description' => 'Follow-up target has passed.',
        'created_by' => $this->staff->id,
        'owner_id' => $this->supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    $this->actingAs($this->admin)
        ->get(route('incidents.index', ['mine' => 1]))
        ->assertOk()
        ->assertSee($ownedByAdmin->title)
        ->assertDontSee($unownedIncident->title)
        ->assertDontSee($overdueIncident->title)
        ->assertSee('Owned by me');

    $this->actingAs($this->admin)
        ->get(route('incidents.index', ['unowned' => 1]))
        ->assertOk()
        ->assertSee($unownedIncident->title)
        ->assertDontSee($ownedByAdmin->title)
        ->assertSee('Unowned');

    $this->actingAs($this->admin)
        ->get(route('incidents.index', ['overdue' => 1]))
        ->assertOk()
        ->assertSee($overdueIncident->title)
        ->assertDontSee($ownedByAdmin->title)
        ->assertSee('Overdue follow-up');
});

test('incident list sanitizes unknown query-driven filters and allows filters to be cleared', function () {
    Livewire::actingAs($this->admin)
        ->withQueryParams([
            'status' => 'Not Real',
            'category' => 'Nope',
            'severity' => 'Critical',
            'unresolved' => 1,
            'stale' => 1,
        ])
        ->test(Index::class)
        ->assertSet('status', '')
        ->assertSet('category', '')
        ->assertSet('severity', '')
        ->assertSet('unresolved', true)
        ->assertSet('stale', true)
        ->call('clearFilters')
        ->assertSet('status', '')
        ->assertSet('category', '')
        ->assertSet('severity', '')
        ->assertSet('unresolved', false)
        ->assertSet('stale', false);
});

test('admin can move incident from open to in progress and create activity trail', function () {
    $component = Livewire::actingAs($this->admin)
        ->test(Show::class, ['incident' => $this->openIncident]);

    $component->set('status', 'In Progress')
        ->call('updateStatus')
        ->assertHasNoErrors()
        ->assertSee('Incident status updated successfully.');

    $incident = $this->openIncident->fresh();

    expect($incident->status)->toBe(IncidentStatus::InProgress);
    expect($incident->resolved_at)->toBeNull();

    $activity = $incident->activities()->latest('id')->first();
    expect($activity->action_type)->toBe('status_changed');
    expect($activity->actor_id)->toBe($this->admin->id);
    expect($activity->summary)->toBe('Status changed from Open to In Progress');
});

test('supervisor can move incident from in progress to resolved and set resolved timestamp', function () {
    Livewire::actingAs($this->supervisor)
        ->test(Show::class, ['incident' => $this->inProgressIncident])
        ->set('status', 'Resolved')
        ->call('updateStatus')
        ->assertHasNoErrors();

    $incident = $this->inProgressIncident->fresh();

    expect($incident->status)->toBe(IncidentStatus::Resolved);
    expect($incident->resolved_at)->not->toBeNull();

    $activity = $incident->activities()->latest('id')->first();
    expect($activity->action_type)->toBe('status_changed');
    expect($activity->actor_id)->toBe($this->supervisor->id);
    expect($activity->summary)->toBe('Status changed from In Progress to Resolved');
});

test('management user can add a next action note when updating incident status', function () {
    Livewire::actingAs($this->admin)
        ->test(Show::class, ['incident' => $this->openIncident])
        ->set('status', 'In Progress')
        ->set('followUpNote', 'Check the device logs and report back to the supervisor.')
        ->call('updateStatus')
        ->assertHasNoErrors()
        ->assertSee('Next action: Check the device logs and report back to the supervisor.');

    $incident = $this->openIncident->fresh();

    expect($incident->status)->toBe(IncidentStatus::InProgress);
    expect($incident->activities()->where('action_type', 'next_action_note')->exists())->toBeTrue();
});

test('management user can assign incident owner and follow-up target', function () {
    Livewire::actingAs($this->admin)
        ->test(Show::class, ['incident' => $this->openIncident])
        ->set('ownerId', (string) $this->supervisor->id)
        ->set('followUpDueAt', '2026-04-21')
        ->call('updateAccountability')
        ->assertHasNoErrors()
        ->assertSee('Incident accountability updated successfully.')
        ->assertSee($this->supervisor->name)
        ->assertSee('Apr 21, 2026');

    $incident = $this->openIncident->fresh();

    expect($incident->owner_id)->toBe($this->supervisor->id);
    expect($incident->follow_up_due_at?->toDateString())->toBe('2026-04-21');
});

test('incident owner must be management-capable', function () {
    Livewire::actingAs($this->admin)
        ->test(Show::class, ['incident' => $this->openIncident])
        ->set('ownerId', (string) $this->staff->id)
        ->call('updateAccountability')
        ->assertHasErrors(['ownerId']);
});

test('management user can clear accountability fields', function () {
    $this->openIncident->update([
        'owner_id' => $this->admin->id,
        'follow_up_due_at' => '2026-04-21',
    ]);

    Livewire::actingAs($this->supervisor)
        ->test(Show::class, ['incident' => $this->openIncident->fresh()])->set('ownerId', '')
        ->set('followUpDueAt', '')
        ->call('updateAccountability')
        ->assertHasNoErrors();

    $incident = $this->openIncident->fresh();

    expect($incident->owner_id)->toBeNull();
    expect($incident->follow_up_due_at)->toBeNull();
});

test('management user can add a resolution summary when resolving an incident', function () {
    Livewire::actingAs($this->admin)
        ->test(Show::class, ['incident' => $this->openIncident])
        ->set('status', 'Resolved')
        ->set('followUpNote', 'Reset the router, confirmed connectivity, and notified the room owner.')
        ->call('updateStatus')
        ->assertHasNoErrors()
        ->assertSee('Resolution: Reset the router, confirmed connectivity, and notified the room owner.');

    $incident = $this->openIncident->fresh();

    expect($incident->status)->toBe(IncidentStatus::Resolved);
    expect($incident->activities()->where('action_type', 'resolution_note')->exists())->toBeTrue();
});

test('supervisor can reopen a resolved incident and clear resolved timestamp', function () {
    expect($this->resolvedIncident->resolved_at)->not->toBeNull();

    Livewire::actingAs($this->supervisor)
        ->test(Show::class, ['incident' => $this->resolvedIncident])
        ->set('status', 'Open')
        ->call('updateStatus')
        ->assertHasNoErrors();

    $incident = $this->resolvedIncident->fresh();

    expect($incident->status)->toBe(IncidentStatus::Open);
    expect($incident->resolved_at)->toBeNull();

    $activity = $incident->activities()->latest('id')->first();
    expect($activity->action_type)->toBe('status_changed');
    expect($activity->actor_id)->toBe($this->supervisor->id);
    expect($activity->summary)->toBe('Status changed from Resolved to Open');
});

test('no-op status update does not create a new activity row', function () {
    $activityCountBefore = $this->openIncident->activities()->count();

    Livewire::actingAs($this->admin)
        ->test(Show::class, ['incident' => $this->openIncident])
        ->set('status', 'Open')
        ->call('updateStatus')
        ->assertHasNoErrors();

    expect($this->openIncident->fresh()->activities()->count())->toBe($activityCountBefore);
});

test('incident detail page renders incident data timeline and null attachment state', function () {
    $this->openIncident->activities()->create([
        'action_type' => 'next_action_note',
        'summary' => 'Next action: Verify the incident detail narrative lane.',
        'actor_id' => $this->admin->id,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)->get(route('incidents.show', $this->openIncident));

    $response->assertOk();
    $response->assertSee($this->openIncident->title);
    $response->assertSee($this->openIncident->category->value);
    $response->assertSee($this->openIncident->severity->value);
    $response->assertSee($this->openIncident->description);
    $response->assertSee('Latest handling context');
    $response->assertSee('Ownership still missing');
    $response->assertSee('Description and evidence');
    $response->assertSee('Accountability lane');
    $response->assertSee('Update status with intent');
    $response->assertSee('Activity timeline');
    $response->assertSee('Next action: Verify the incident detail narrative lane.');
    $response->assertSee('data-severity="'.$this->openIncident->severity->value.'"', false);
    $response->assertSee('Reported');
    $response->assertDontSee('View attachment');
});

test('incident detail page shows attachment link when attachment exists', function () {
    $incidentWithAttachment = Incident::create([
        'title' => 'Attached proof incident',
        'category' => 'อุปกรณ์คอมพิวเตอร์',
        'severity' => 'High',
        'status' => 'Open',
        'description' => 'Attachment present for verification.',
        'attachment_path' => 'incidents/evidence.pdf',
        'created_by' => $this->staff->id,
    ]);

    $incidentWithAttachment->activities()->create([
        'action_type' => 'created',
        'summary' => 'Incident reported',
        'actor_id' => $this->staff->id,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->supervisor)->get(route('incidents.show', $incidentWithAttachment));

    $response->assertOk();
    $response->assertSee('View attachment');
    $response->assertSee(asset('storage/incidents/evidence.pdf'), false);
});

test('incident detail page shows age and stale indicators for old unresolved incidents', function () {
    $oldIncident = Incident::create([
        'title' => 'Old unresolved incident',
        'category' => 'เครือข่าย',
        'severity' => 'High',
        'status' => 'Open',
        'description' => 'Needs follow-up because it has been open for days.',
        'created_by' => $this->staff->id,
    ]);

    $oldIncident->forceFill([
        'created_at' => now()->subDays(3),
        'updated_at' => now()->subDays(3),
    ])->saveQuietly();

    $oldIncident->activities()->create([
        'action_type' => 'created',
        'summary' => 'Incident reported',
        'actor_id' => $this->staff->id,
        'created_at' => now()->subDays(3),
    ]);

    $response = $this->actingAs($this->supervisor)->get(route('incidents.show', $oldIncident));

    $response->assertOk();
    $response->assertSee('Open for 3 days');
    $response->assertSee('Stale');
});

test('incident detail page highlights overdue follow-up pressure', function () {
    $overdueIncident = Incident::create([
        'title' => 'Overdue follow-up detail incident',
        'category' => 'เครือข่าย',
        'severity' => 'High',
        'status' => 'In Progress',
        'description' => 'Needs review because the follow-up date passed.',
        'created_by' => $this->staff->id,
        'owner_id' => $this->supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    $overdueIncident->activities()->create([
        'action_type' => 'created',
        'summary' => 'Incident reported',
        'actor_id' => $this->staff->id,
        'created_at' => now()->subDays(2),
    ]);

    $response = $this->actingAs($this->supervisor)->get(route('incidents.show', $overdueIncident));

    $response->assertOk();
    $response->assertSee('Follow-up target overdue');
    $response->assertSee('Follow-up overdue');
});
