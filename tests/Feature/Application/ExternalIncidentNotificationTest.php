<?php

declare(strict_types=1);

use App\Application\Incidents\Actions\CreateIncident;
use App\Application\Incidents\Actions\TransitionIncidentStatus;
use App\Application\Incidents\Actions\UpdateIncidentAccountability;
use App\Application\Incidents\Listeners\SendExternalNotificationOnIncidentEvent;
use App\Domain\Access\Enums\UserRole;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Domain\Incidents\Events\IncidentAccountabilityChanged;
use App\Domain\Incidents\Events\IncidentCreated;
use App\Domain\Incidents\Events\IncidentStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->staff = $this->createUserForRole(UserRole::Staff);
    $this->admin = $this->createUserForRole(UserRole::Admin);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->room = $this->createRoom(['name' => 'Notification Lab', 'code' => 'NOTIF-1']);
});

// ── Event Dispatch Tests ────────────────────────────────────────────

test('creating an incident dispatches an IncidentCreated event', function () {
    Event::fake([IncidentCreated::class]);

    $incident = app(CreateIncident::class)([
        'title' => 'Event dispatch test',
        'category' => 'เครือข่าย',
        'subcategory' => 'อินเทอร์เน็ต',
        'severity' => 'High',
        'description' => 'Verify the domain event is dispatched after commit.',
        'room_id' => $this->room->id,
    ], $this->staff->id);

    Event::assertDispatched(IncidentCreated::class, function (IncidentCreated $event) use ($incident): bool {
        return $event->incidentId === $incident->id;
    });
});

test('transitioning an incident status dispatches an IncidentStatusChanged event', function () {
    Event::fake([IncidentStatusChanged::class]);

    $incident = $this->createIncidentWithActivity($this->staff, [
        'title' => 'Status event test',
        'status' => IncidentStatus::Open->value,
        'room_id' => $this->room->id,
    ], room: $this->room);

    app(TransitionIncidentStatus::class)($incident, IncidentStatus::InProgress->value, $this->admin->id);

    Event::assertDispatched(IncidentStatusChanged::class, function (IncidentStatusChanged $event) use ($incident): bool {
        return $event->incidentId === $incident->id
            && $event->previousStatus === IncidentStatus::Open->value;
    });
});

test('no-op status transitions do not dispatch events', function () {
    Event::fake([IncidentStatusChanged::class]);

    $incident = $this->createIncidentWithActivity($this->staff, [
        'title' => 'No-op status test',
        'status' => IncidentStatus::Open->value,
        'room_id' => $this->room->id,
    ], room: $this->room);

    app(TransitionIncidentStatus::class)($incident, IncidentStatus::Open->value, $this->admin->id);

    Event::assertNotDispatched(IncidentStatusChanged::class);
});

test('updating accountability dispatches an IncidentAccountabilityChanged event', function () {
    Event::fake([IncidentAccountabilityChanged::class]);

    $incident = $this->createIncidentWithActivity($this->staff, [
        'title' => 'Accountability event test',
        'status' => IncidentStatus::Open->value,
        'room_id' => $this->room->id,
    ], room: $this->room);

    app(UpdateIncidentAccountability::class)($incident, $this->supervisor->id, '2026-05-20', $this->admin->id);

    Event::assertDispatched(IncidentAccountabilityChanged::class, function (IncidentAccountabilityChanged $event) use ($incident): bool {
        return $event->incidentId === $incident->id;
    });
});

test('no-op accountability updates do not dispatch events', function () {
    Event::fake([IncidentAccountabilityChanged::class]);

    $incident = $this->createIncidentWithActivity($this->staff, [
        'title' => 'No-op accountability test',
        'status' => IncidentStatus::Open->value,
        'room_id' => $this->room->id,
    ], room: $this->room);

    app(UpdateIncidentAccountability::class)($incident->fresh(['owner']), null, null, $this->admin->id);

    Event::assertNotDispatched(IncidentAccountabilityChanged::class);
});

// ── Listener Integration Tests ──────────────────────────────────────

test('listener sends LINE push when notifications are enabled', function () {
    config([
        'services.line.notifications.enabled' => true,
        'services.line.notifications.channel_access_token' => 'test-token',
        'services.line.notifications.to' => 'C1234567890',
    ]);

    Http::fake([
        'https://api.line.me/v2/bot/message/push' => Http::response([], 200),
    ]);

    $incident = app(CreateIncident::class)([
        'title' => 'LINE enabled incident',
        'category' => 'เครือข่าย',
        'subcategory' => 'อินเทอร์เน็ต',
        'severity' => 'High',
        'description' => 'Verify the listener delivers to LINE.',
        'room_id' => $this->room->id,
    ], $this->staff->id);

    $listener = new SendExternalNotificationOnIncidentEvent;
    $listener->onCreated(new IncidentCreated($incident->id));

    Http::assertSent(function (Request $request) use ($incident): bool {
        $payload = $request->data();

        return $request->url() === 'https://api.line.me/v2/bot/message/push'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $payload['to'] === 'C1234567890'
            && $payload['messages'][0]['type'] === 'text'
            && str_contains($payload['messages'][0]['text'], 'มีรายงานปัญหาใหม่')
            && str_contains($payload['messages'][0]['text'], $incident->title)
            && str_contains($payload['messages'][0]['text'], 'Notification Lab');
    });
});

test('listener does not send LINE push when notifications are disabled', function () {
    Http::fake();

    $incident = app(CreateIncident::class)([
        'title' => 'Disabled notification incident',
        'category' => 'เครือข่าย',
        'subcategory' => 'อินเทอร์เน็ต',
        'severity' => 'High',
        'description' => 'Listener should not call LINE.',
        'room_id' => $this->room->id,
    ], $this->staff->id);

    $listener = new SendExternalNotificationOnIncidentEvent;
    $listener->onCreated(new IncidentCreated($incident->id));

    Http::assertNothingSent();
});

test('listener tolerates LINE API failures without throwing', function () {
    config([
        'services.line.notifications.enabled' => true,
        'services.line.notifications.channel_access_token' => 'test-token',
        'services.line.notifications.to' => 'C1234567890',
    ]);

    Http::fake([
        'https://api.line.me/v2/bot/message/push' => Http::response(['message' => 'failed'], 500),
    ]);

    $incident = $this->createIncidentWithActivity($this->staff, [
        'title' => 'Failure-tolerant incident',
        'status' => IncidentStatus::Open->value,
        'room_id' => $this->room->id,
    ], room: $this->room);

    $listener = new SendExternalNotificationOnIncidentEvent;

    // Must not throw — failure is logged but not propagated.
    $listener->onStatusChanged(new IncidentStatusChanged($incident->id, IncidentStatus::Open->value));

    Http::assertSentCount(1);
});

test('listener sends accountability change notifications', function () {
    config([
        'services.line.notifications.enabled' => true,
        'services.line.notifications.channel_access_token' => 'test-token',
        'services.line.notifications.to' => 'C1234567890',
    ]);

    Http::fake([
        'https://api.line.me/v2/bot/message/push' => Http::response([], 200),
    ]);

    $incident = $this->createIncidentWithActivity($this->staff, [
        'title' => 'Accountability notification incident',
        'status' => IncidentStatus::Open->value,
        'room_id' => $this->room->id,
    ], room: $this->room);

    app(UpdateIncidentAccountability::class)($incident, $this->supervisor->id, '2026-05-20', $this->admin->id);

    $listener = new SendExternalNotificationOnIncidentEvent;
    $listener->onAccountabilityChanged(new IncidentAccountabilityChanged($incident->id));

    Http::assertSent(function (Request $request): bool {
        $payload = $request->data();

        return str_contains($payload['messages'][0]['text'], 'อัปเดตผู้รับผิดชอบ/กำหนดติดตาม')
            && str_contains($payload['messages'][0]['text'], $this->supervisor->name);
    });
});

test('listener handles deleted incidents gracefully', function () {
    Http::fake();

    $listener = new SendExternalNotificationOnIncidentEvent;

    // Incident ID that doesn't exist — listener should return silently.
    $listener->onCreated(new IncidentCreated(999999));

    Http::assertNothingSent();
});
