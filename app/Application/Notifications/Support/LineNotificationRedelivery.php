<?php

declare(strict_types=1);

namespace App\Application\Notifications\Support;

use App\Models\NotificationDelivery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class LineNotificationRedelivery
{
    public function __construct(
        protected LineNotificationRecipientResolver $recipients,
    ) {}

    /**
     * Re-send an operational notification from a previous delivery record.
     *
     * This deliberately sends a "manual redelivery" message instead of trying
     * to recreate the original message payload, because delivery records do not
     * store the original payload or previous domain state.
     *
     * @return array{status:string,http_status:?int,message:string,recipient_type:?string,recipient_fingerprint:?string}
     */
    public function __invoke(NotificationDelivery $delivery): array
    {
        if (! $this->canRedeliver($delivery)) {
            throw new InvalidArgumentException('This notification delivery cannot be redelivered.');
        }

        if (! (bool) config('services.line.notifications.enabled', false)) {
            return $this->record(
                sourceDelivery: $delivery,
                status: 'skipped_disabled',
                message: 'LINE notifications are disabled.',
            );
        }

        $token = (string) config('services.line.notifications.channel_access_token', '');
        $recipients = $this->recipients->forEvent('manual_redelivery');

        if ($token === '' || $recipients === []) {
            Log::warning('LINE notification redelivery skipped because credentials are incomplete.', [
                'notification_delivery_id' => $delivery->id,
                'incident_id' => $delivery->incident_id,
            ]);

            return $this->record(
                sourceDelivery: $delivery,
                status: 'skipped_incomplete_config',
                message: 'LINE credentials are incomplete.',
                recipient: $recipients[0] ?? null,
            );
        }

        $finalResult = null;

        foreach ($recipients as $recipient) {
            try {
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->asJson()
                    ->timeout((int) config('services.line.notifications.timeout', 5))
                    ->post('https://api.line.me/v2/bot/message/push', [
                        'to' => $recipient,
                        'messages' => [[
                            'type' => 'text',
                            'text' => $this->message($delivery),
                        ]],
                    ]);

                if ($response->failed()) {
                    $finalResult = $this->record(
                        sourceDelivery: $delivery,
                        status: 'failed',
                        httpStatus: $response->status(),
                        message: $this->summarizeResponseBody($response->body()) ?? 'LINE push failed.',
                        recipient: $recipient,
                    );

                    continue;
                }

                $finalResult = $this->record(
                    sourceDelivery: $delivery,
                    status: 'sent',
                    httpStatus: $response->status(),
                    message: 'LINE manual redelivery accepted.',
                    recipient: $recipient,
                );
            } catch (\Throwable $exception) {
                Log::warning('LINE notification redelivery could not be delivered.', [
                    'notification_delivery_id' => $delivery->id,
                    'incident_id' => $delivery->incident_id,
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ]);

                $finalResult = $this->record(
                    sourceDelivery: $delivery,
                    status: 'failed_exception',
                    message: $exception::class.': '.$exception->getMessage(),
                    recipient: $recipient,
                );
            }
        }

        return $finalResult ?? $this->record(
            sourceDelivery: $delivery,
            status: 'skipped_incomplete_config',
            message: 'LINE recipients are incomplete.',
        );
    }

    public function canRedeliver(NotificationDelivery $delivery): bool
    {
        return $delivery->channel === 'line'
            && $delivery->incident_id !== null
            && $delivery->incident !== null
            && $delivery->status !== 'sent'
            && in_array($delivery->event_type, [
                'incident_created',
                'incident_status_changed',
                'incident_accountability_changed',
            ], true);
    }

    protected function message(NotificationDelivery $delivery): string
    {
        $incident = $delivery->incident;

        return implode("\n", array_filter([
            '[Daily Ops] ส่งซ้ำการแจ้งเตือน',
            "#{$incident->id} {$incident->title}",
            'เหตุการณ์เดิม: '.$this->eventLabel($delivery->event_type),
            sprintf('ห้อง: %s', $incident->room?->name ?? '-'),
            sprintf('สถานะปัจจุบัน: %s', $incident->status->value),
            'เปิดดู: '.route('incidents.show', $incident),
        ]));
    }

    protected function eventLabel(string $eventType): string
    {
        return match ($eventType) {
            'incident_created' => 'รายงานปัญหาใหม่',
            'incident_status_changed' => 'เปลี่ยนสถานะปัญหา',
            'incident_accountability_changed' => 'เปลี่ยนผู้รับผิดชอบ',
            default => $eventType,
        };
    }

    /**
     * @return array{status:string,http_status:?int,message:string,recipient_type:?string,recipient_fingerprint:?string}
     */
    protected function record(
        NotificationDelivery $sourceDelivery,
        string $status,
        string $message,
        ?int $httpStatus = null,
        ?string $recipient = null,
    ): array {
        $recipientType = $this->recipientType($recipient);
        $recipientFingerprint = $this->recipientFingerprint($recipient);

        NotificationDelivery::query()->create([
            'incident_id' => $sourceDelivery->incident_id,
            'channel' => 'line',
            'event_type' => 'manual_redelivery',
            'recipient_type' => $recipientType,
            'recipient_fingerprint' => $recipientFingerprint,
            'status' => $status,
            'http_status' => $httpStatus,
            'message' => mb_substr($message, 0, 500),
            'attempted_at' => now(),
        ]);

        return [
            'status' => $status,
            'http_status' => $httpStatus,
            'message' => $message,
            'recipient_type' => $recipientType,
            'recipient_fingerprint' => $recipientFingerprint,
        ];
    }

    protected function recipientType(?string $recipient): ?string
    {
        return match ($recipient !== null ? substr($recipient, 0, 1) : null) {
            'U' => 'user',
            'C' => 'group',
            'R' => 'room',
            default => null,
        };
    }

    protected function recipientFingerprint(?string $recipient): ?string
    {
        if ($recipient === null || $recipient === '') {
            return null;
        }

        return substr(hash('sha256', $recipient), 0, 16);
    }

    protected function summarizeResponseBody(string $body): ?string
    {
        $body = trim($body);

        return $body !== '' ? mb_substr($body, 0, 500) : null;
    }
}
