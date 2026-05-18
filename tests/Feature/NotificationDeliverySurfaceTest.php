<?php

use App\Domain\Access\Enums\UserRole;
use App\Livewire\Management\Notifications\DeliveryIndex;
use App\Models\Incident;
use App\Models\NotificationDelivery;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-05-19 09:00:00'));

    $this->admin = $this->createUserForRole(UserRole::Admin);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->staff = $this->createUserForRole(UserRole::Staff);
    $this->room = $this->createRoom(['name' => 'Notification Lab', 'code' => 'NOTI-1']);

    $this->incident = Incident::factory()->create([
        'title' => 'LINE audit verification issue',
        'category' => 'เครือข่าย',
        'subcategory' => 'อินเทอร์เน็ต',
        'severity' => 'Low',
        'room_id' => $this->room->id,
        'created_by' => $this->staff->id,
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

test('management users can access notification delivery log and staff cannot', function () {
    $this->actingAs($this->admin)
        ->get(route('notifications.deliveries.index'))
        ->assertOk();

    $this->actingAs($this->supervisor)
        ->get(route('notifications.deliveries.index'))
        ->assertOk();

    $this->actingAs($this->staff)
        ->get(route('notifications.deliveries.index'))
        ->assertForbidden();
});

test('notification delivery page renders audit rows without exposing recipient ids', function () {
    NotificationDelivery::query()->create([
        'incident_id' => $this->incident->id,
        'channel' => 'line',
        'event_type' => 'incident_created',
        'recipient_type' => 'user',
        'recipient_fingerprint' => 'abcd1234fingerpr',
        'status' => 'sent',
        'http_status' => 200,
        'message' => 'LINE push accepted.',
        'attempted_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('notifications.deliveries.index'));

    $response->assertOk();
    $response->assertSee('ประวัติการส่งแจ้งเตือน');
    $response->assertSee('LINE audit verification issue');
    $response->assertSee('รายงานปัญหาใหม่');
    $response->assertSee('ส่งสำเร็จ');
    $response->assertSee('fp: abcd1234fingerpr');
    $response->assertDontSee('wire:click="redeliver', false);
    $response->assertDontSee('U1234567890abcdef', false);
});

test('notification delivery livewire filters by status and event type', function () {
    NotificationDelivery::query()->create([
        'incident_id' => $this->incident->id,
        'channel' => 'line',
        'event_type' => 'incident_created',
        'recipient_type' => 'user',
        'recipient_fingerprint' => 'sentfingerprint',
        'status' => 'sent',
        'http_status' => 200,
        'message' => 'LINE push accepted.',
        'attempted_at' => now(),
    ]);

    NotificationDelivery::query()->create([
        'incident_id' => $this->incident->id,
        'channel' => 'line',
        'event_type' => 'incident_status_changed',
        'recipient_type' => 'user',
        'recipient_fingerprint' => 'failedfingerprin',
        'status' => 'failed',
        'http_status' => 401,
        'message' => 'LINE push failed with HTTP 401.',
        'attempted_at' => now(),
    ]);

    Livewire::actingAs($this->supervisor)
        ->test(DeliveryIndex::class)
        ->assertSee('LINE audit verification issue')
        ->set('status', 'failed')
        ->assertSee('LINE push failed with HTTP 401.')
        ->assertDontSee('LINE push accepted.')
        ->set('eventType', 'incident_created')
        ->assertSee('ยังไม่มี delivery log ในช่วงนี้')
        ->set('status', 'sent')
        ->assertSee('LINE push accepted.')
        ->assertDontSee('LINE push failed with HTTP 401.');
});

test('management users can manually redeliver failed incident notifications', function () {
    config([
        'services.line.notifications.enabled' => true,
        'services.line.notifications.channel_access_token' => 'test-token',
        'services.line.notifications.to' => 'U1234567890abcdef',
    ]);

    Http::fake([
        'https://api.line.me/v2/bot/message/push' => Http::response([], 200),
    ]);

    $delivery = NotificationDelivery::query()->create([
        'incident_id' => $this->incident->id,
        'channel' => 'line',
        'event_type' => 'incident_created',
        'recipient_type' => 'user',
        'recipient_fingerprint' => 'failedfingerprin',
        'status' => 'failed',
        'http_status' => 401,
        'message' => 'LINE push failed with HTTP 401.',
        'attempted_at' => now(),
    ]);

    Livewire::actingAs($this->admin)
        ->test(DeliveryIndex::class)
        ->assertSee('ส่งซ้ำ')
        ->call('redeliver', $delivery->id)
        ->assertSee('ส่งซ้ำไปยัง LINE สำเร็จ');

    Http::assertSent(function (Request $request): bool {
        $payload = $request->data();

        return $request->hasHeader('Authorization', 'Bearer test-token')
            && $payload['to'] === 'U1234567890abcdef'
            && str_contains($payload['messages'][0]['text'], 'ส่งซ้ำการแจ้งเตือน')
            && str_contains($payload['messages'][0]['text'], 'LINE audit verification issue')
            && str_contains($payload['messages'][0]['text'], 'เหตุการณ์เดิม: รายงานปัญหาใหม่');
    });

    $redelivery = NotificationDelivery::query()
        ->where('event_type', 'manual_redelivery')
        ->firstOrFail();

    expect($redelivery->incident_id)->toBe($this->incident->id)
        ->and($redelivery->status)->toBe('sent')
        ->and($redelivery->http_status)->toBe(200)
        ->and($redelivery->recipient_type)->toBe('user')
        ->and($redelivery->recipient_fingerprint)->toHaveLength(16);
});

test('staff cannot manually redeliver notification deliveries through livewire', function () {
    $delivery = NotificationDelivery::query()->create([
        'incident_id' => $this->incident->id,
        'channel' => 'line',
        'event_type' => 'incident_created',
        'recipient_type' => 'user',
        'recipient_fingerprint' => 'failedfingerprin',
        'status' => 'failed',
        'http_status' => 401,
        'message' => 'LINE push failed with HTTP 401.',
        'attempted_at' => now(),
    ]);

    Livewire::actingAs($this->staff)
        ->test(DeliveryIndex::class)
        ->call('redeliver', $delivery->id)
        ->assertForbidden();
});
