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

test('management-only route access applies to printable incident summary', function () {
    $incident = $this->createIncidentWithActivity($this->admin, [
        'title' => 'Printable incident route record',
        'status' => IncidentStatus::Open->value,
    ]);

    $this->get(route('incidents.print', $incident))->assertRedirect('/login');

    $this->actingAs($this->admin)->get(route('incidents.print', $incident))->assertOk();
    $this->actingAs($this->supervisor)->get(route('incidents.print', $incident))->assertOk();
    $this->actingAs($this->staff)->get(route('incidents.print', $incident))->assertForbidden();
});

test('incident history shows recent opened and resolved slices for selected range', function () {
    $owner = $this->createUserForRole(UserRole::Supervisor, [
        'name' => 'History Owner',
        'email' => 'history-owner@example.com',
    ]);
    $room = $this->createRoom([
        'name' => 'Lab History 1',
        'code' => 'LAB-H1',
    ]);

    $openedStillActive = $this->createIncidentWithActivity($this->admin, [
        'title' => 'History still active incident',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
        'owner_id' => $owner->id,
        'equipment_reference' => 'PC-21',
        'created_at' => now()->subDays(1),
    ], room: $room);

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
    $response->assertSee('ประวัติรายงานปัญหา');
    $response->assertSee('การเคลื่อนไหวของรายงานปัญหาล่าสุด');
    $response->assertSee('ยังไม่ปิด');
    $response->assertSee($openedStillActive->title);
    $response->assertSee($room->name);
    $response->assertSee('PC-21');
    $response->assertSee($resolvedRecently->title);
    $response->assertSee($owner->name);
    $response->assertDontSee($oldIncident->title);

    $this->actingAs($this->admin)
        ->get('/incidents/history?days=30')
        ->assertSee($openedStillActive->title)
        ->assertSee($resolvedRecently->title);
});

test('incident history applies day boundary windows without pulling in out-of-range records', function () {
    $boundaryOpened = $this->createIncidentWithActivity($this->admin, [
        'title' => 'Boundary opened incident',
        'status' => IncidentStatus::Open->value,
        'created_at' => now()->subDays(6)->startOfDay(),
    ]);

    $boundaryResolved = $this->createIncidentWithActivity($this->supervisor, [
        'title' => 'Boundary resolved incident',
        'status' => IncidentStatus::Resolved->value,
        'created_at' => now()->subDays(20),
        'resolved_at' => now()->subDays(6)->startOfDay(),
    ]);

    $justOutsideRange = $this->createIncidentWithActivity($this->admin, [
        'title' => 'Outside range boundary incident',
        'status' => IncidentStatus::Resolved->value,
        'created_at' => now()->subDays(20),
        'resolved_at' => now()->subDays(7)->subSecond(),
    ]);

    $response = $this->actingAs($this->admin)->get('/incidents/history');

    $response->assertOk();
    $response->assertSee($boundaryOpened->title);
    $response->assertSee($boundaryResolved->title);
    $response->assertDontSee($justOutsideRange->title);
});

test('printable incident summary shows evidence and accountability snapshot', function () {
    $owner = $this->createUserForRole(UserRole::Supervisor, [
        'name' => 'Printable Summary Owner',
        'email' => 'print-owner@example.com',
    ]);
    $room = $this->createRoom([
        'name' => 'Lab Print 2',
        'code' => 'LAB-P2',
    ]);

    $incident = $this->createIncidentWithActivity($this->admin, [
        'title' => 'Printer queue jam on lab floor',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::InProgress->value,
        'owner_id' => $owner->id,
        'follow_up_due_at' => now()->addDay(),
        'equipment_reference' => 'Printer-Queue-A',
    ], room: $room);

    $response = $this->actingAs($this->admin)->get(route('incidents.print', $incident));

    $response->assertOk();
    $response->assertSee('มุมมองพิมพ์สรุปรายงานปัญหา');
    $response->assertSee('พิมพ์สรุปรายงาน');
    $response->assertSee('Printer queue jam on lab floor');
    $response->assertSee($room->name);
    $response->assertSee('Printer-Queue-A');
    $response->assertSee('สรุปภาระการติดตาม');
    $response->assertSee($owner->name);
    $response->assertSee('ลำดับเหตุการณ์');
});
