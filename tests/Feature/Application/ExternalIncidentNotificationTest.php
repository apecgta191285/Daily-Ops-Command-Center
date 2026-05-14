<?php

use App\Application\Incidents\Actions\CreateIncident;
use App\Application\Incidents\Actions\TransitionIncidentStatus;
use App\Application\Incidents\Actions\UpdateIncidentAccountability;
use App\Domain\Access\Enums\UserRole;
use App\Domain\Incidents\Enums\IncidentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->staff = $this->createUserForRole(UserRole::Staff);
    $this->admin = $this->createUserForRole(UserRole::Admin);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->room = $this->createRoom(['name' => 'Notification Lab', 'code' => 'NOTIF-1']);
});

test('line incident notifications are disabled by default', function () {
    Http::fake();

    app(CreateIncident::class)([
        'title' => 'Disabled notification incident',
        'category' => 'เครือข่าย',
        'subcategory' => 'อินเทอร์เน็ต',
        'severity' => 'High',
        'description' => 'The notification channel should not be called.',
        'room_id' => $this->room->id,
    ], $this->staff->id);

    Http::assertNothingSent();
});

test('line incident notifications send a push message when enabled', function () {
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
        'description' => 'The notification channel should receive this incident.',
        'room_id' => $this->room->id,
    ], $this->staff->id);

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

test('line notification failures do not block incident status changes', function () {
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

    $result = app(TransitionIncidentStatus::class)($incident, IncidentStatus::InProgress->value, $this->admin->id);

    expect($result->changed)->toBeTrue();
    expect($result->incident->status)->toBe(IncidentStatus::InProgress);

    Http::assertSentCount(1);
});

test('no-op incident updates do not send external notifications', function () {
    config([
        'services.line.notifications.enabled' => true,
        'services.line.notifications.channel_access_token' => 'test-token',
        'services.line.notifications.to' => 'C1234567890',
    ]);

    Http::fake();

    $incident = $this->createIncidentWithActivity($this->staff, [
        'title' => 'No-op notification incident',
        'status' => IncidentStatus::Open->value,
        'room_id' => $this->room->id,
    ], room: $this->room);

    app(TransitionIncidentStatus::class)($incident, IncidentStatus::Open->value, $this->admin->id);
    app(UpdateIncidentAccountability::class)($incident->fresh(['owner']), null, null, $this->admin->id);

    Http::assertNothingSent();
});

test('accountability changes send external notifications when enabled', function () {
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

    Http::assertSent(function (Request $request): bool {
        $payload = $request->data();

        return str_contains($payload['messages'][0]['text'], 'อัปเดตผู้รับผิดชอบ/กำหนดติดตาม')
            && str_contains($payload['messages'][0]['text'], $this->supervisor->name)
            && str_contains($payload['messages'][0]['text'], '2026-05-20');
    });
});
