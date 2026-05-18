<?php

declare(strict_types=1);

use App\Models\NotificationDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('line notification probe sends a test message and records the delivery', function () {
    config([
        'services.line.notifications.enabled' => true,
        'services.line.notifications.channel_access_token' => 'test-token',
        'services.line.notifications.to' => 'U1234567890abcdef',
    ]);

    Http::fake([
        'https://api.line.me/v2/bot/message/push' => Http::response([], 200),
    ]);

    $this->artisan('notifications:line:test', [
        '--message' => 'Manual LINE probe',
    ])->assertExitCode(0);

    Http::assertSent(function (Request $request): bool {
        $payload = $request->data();

        return $request->url() === 'https://api.line.me/v2/bot/message/push'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $payload['to'] === 'U1234567890abcdef'
            && $payload['messages'][0]['text'] === 'Manual LINE probe';
    });

    $delivery = NotificationDelivery::query()->firstOrFail();

    expect($delivery->incident_id)->toBeNull()
        ->and($delivery->event_type)->toBe('manual_test')
        ->and($delivery->status)->toBe('sent')
        ->and($delivery->http_status)->toBe(200)
        ->and($delivery->recipient_type)->toBe('user')
        ->and($delivery->recipient_fingerprint)->toHaveLength(16);
});

test('line notification probe fails closed when credentials are incomplete', function () {
    config([
        'services.line.notifications.enabled' => true,
        'services.line.notifications.channel_access_token' => '',
        'services.line.notifications.to' => '',
    ]);

    Http::fake();

    $this->artisan('notifications:line:test')->assertExitCode(1);

    Http::assertNothingSent();

    $delivery = NotificationDelivery::query()->firstOrFail();

    expect($delivery->event_type)->toBe('manual_test')
        ->and($delivery->status)->toBe('skipped_incomplete_config')
        ->and($delivery->recipient_fingerprint)->toBeNull();
});

test('line notification probe records api failures for audit review', function () {
    config([
        'services.line.notifications.enabled' => true,
        'services.line.notifications.channel_access_token' => 'test-token',
        'services.line.notifications.to' => 'C1234567890abcdef',
    ]);

    Http::fake([
        'https://api.line.me/v2/bot/message/push' => Http::response(['message' => 'Invalid token'], 401),
    ]);

    $this->artisan('notifications:line:test')->assertExitCode(1);

    $delivery = NotificationDelivery::query()->firstOrFail();

    expect($delivery->event_type)->toBe('manual_test')
        ->and($delivery->status)->toBe('failed')
        ->and($delivery->http_status)->toBe(401)
        ->and($delivery->message)->toContain('Invalid token')
        ->and($delivery->recipient_type)->toBe('group');
});
