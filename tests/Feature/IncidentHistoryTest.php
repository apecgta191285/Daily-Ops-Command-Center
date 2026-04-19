<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = $this->createUserForRole(UserRole::Admin);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->staff = $this->createUserForRole(UserRole::Staff);
});

test('management-only route access applies to incident history', function () {
    $this->get('/incidents/history')->assertRedirect('/login');

    $this->actingAs($this->admin)->get('/incidents/history')->assertOk();
    $this->actingAs($this->supervisor)->get('/incidents/history')->assertOk();
    $this->actingAs($this->staff)->get('/incidents/history')->assertForbidden();
});

test('incident history shows recent opened and resolved slices for selected range', function () {
    $owner = $this->createUserForRole(UserRole::Supervisor, [
        'name' => 'History Owner',
        'email' => 'history-owner@example.com',
    ]);

    $openedStillActive = $this->createIncidentWithActivity($this->admin, [
        'title' => 'History still active incident',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
        'owner_id' => $owner->id,
        'created_at' => now()->subDays(1),
    ]);

    $resolvedRecently = $this->createIncidentWithActivity($this->supervisor, [
        'title' => 'History resolved incident',
        'severity' => IncidentSeverity::Medium->value,
        'status' => IncidentStatus::Resolved->value,
        'owner_id' => $owner->id,
        'created_at' => now()->subDays(3),
        'resolved_at' => now()->subDays(1),
    ]);

    $oldIncident = $this->createIncidentWithActivity($this->admin, [
        'title' => 'History outside range incident',
        'severity' => IncidentSeverity::Low->value,
        'status' => IncidentStatus::Resolved->value,
        'created_at' => now()->subDays(40),
        'resolved_at' => now()->subDays(35),
    ]);

    $response = $this->actingAs($this->admin)->get('/incidents/history');

    $response->assertOk();
    $response->assertSee('Incident History');
    $response->assertSee('Recent incident movement');
    $response->assertSee('Still active');
    $response->assertSee($openedStillActive->title);
    $response->assertSee($resolvedRecently->title);
    $response->assertSee($owner->name);
    $response->assertDontSee($oldIncident->title);

    $this->actingAs($this->admin)
        ->get('/incidents/history?days=30')
        ->assertSee($openedStillActive->title)
        ->assertSee($resolvedRecently->title);
});
