<?php

declare(strict_types=1);

namespace App\Application\Notifications\Support;

use App\Models\NotificationDelivery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineNotificationProbe
{
    /**
     * Send a deliberately small LINE test message and record the delivery outcome.
     *
     * @return array{status:string,http_status:?int,message:string,recipient_type:?string,recipient_fingerprint:?string}
     */
    public function __invoke(?string $message = null): array
    {
        if (! (bool) config('services.line.notifications.enabled', false)) {
            return $this->record(
                status: 'skipped_disabled',
                message: 'LINE notifications are disabled.',
            );
        }

        $token = (string) config('services.line.notifications.channel_access_token', '');
        $to = (string) config('services.line.notifications.to', '');

        if ($token === '' || $to === '') {
            Log::warning('LINE notification probe skipped because credentials are incomplete.');

            return $this->record(
                status: 'skipped_incomplete_config',
                message: 'LINE credentials are incomplete.',
                recipient: $to !== '' ? $to : null,
            );
        }

        $text = $message !== null && trim($message) !== ''
            ? trim($message)
            : sprintf('[Daily Ops] LINE connection test from %s at %s', config('app.name'), now()->format('Y-m-d H:i:s'));

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('services.line.notifications.timeout', 5))
                ->post('https://api.line.me/v2/bot/message/push', [
                    'to' => $to,
                    'messages' => [[
                        'type' => 'text',
                        'text' => $text,
                    ]],
                ]);

            if ($response->failed()) {
                return $this->record(
                    status: 'failed',
                    httpStatus: $response->status(),
                    message: $this->summarizeResponseBody($response->body()) ?? 'LINE push failed.',
                    recipient: $to,
                );
            }

            return $this->record(
                status: 'sent',
                httpStatus: $response->status(),
                message: 'LINE push accepted.',
                recipient: $to,
            );
        } catch (\Throwable $exception) {
            Log::warning('LINE notification probe could not be delivered.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return $this->record(
                status: 'failed_exception',
                message: $exception::class.': '.$exception->getMessage(),
                recipient: $to,
            );
        }
    }

    /**
     * @return array{status:string,http_status:?int,message:string,recipient_type:?string,recipient_fingerprint:?string}
     */
    protected function record(
        string $status,
        string $message,
        ?int $httpStatus = null,
        ?string $recipient = null,
    ): array {
        $recipientType = $this->recipientType($recipient);
        $recipientFingerprint = $this->recipientFingerprint($recipient);

        NotificationDelivery::query()->create([
            'incident_id' => null,
            'channel' => 'line',
            'event_type' => 'manual_test',
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
