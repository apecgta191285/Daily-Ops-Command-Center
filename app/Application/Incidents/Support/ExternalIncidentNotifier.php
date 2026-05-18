<?php

declare(strict_types=1);

namespace App\Application\Incidents\Support;

use App\Models\Incident;
use App\Models\NotificationDelivery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalIncidentNotifier
{
    public function incidentCreated(Incident $incident): void
    {
        $this->send(
            eventType: 'incident_created',
            title: 'มีรายงานปัญหาใหม่',
            incident: $incident,
            detail: sprintf(
                "ห้อง: %s\nหมวด: %s / %s\nความรุนแรง: %s",
                $incident->room?->name ?? '-',
                $incident->category->value,
                $incident->subcategory ?? '-',
                $incident->severity->value,
            ),
        );
    }

    public function statusChanged(Incident $incident, string $previousStatus): void
    {
        $this->send(
            eventType: 'incident_status_changed',
            title: 'อัปเดตสถานะรายงานปัญหา',
            incident: $incident,
            detail: sprintf(
                "สถานะ: %s -> %s\nห้อง: %s",
                $previousStatus,
                $incident->status->value,
                $incident->room?->name ?? '-',
            ),
        );
    }

    public function accountabilityChanged(Incident $incident): void
    {
        $this->send(
            eventType: 'incident_accountability_changed',
            title: 'อัปเดตผู้รับผิดชอบ/กำหนดติดตาม',
            incident: $incident,
            detail: sprintf(
                "ผู้รับผิดชอบ: %s\nกำหนดติดตาม: %s",
                $incident->owner?->name ?? '-',
                $incident->follow_up_due_at?->format('Y-m-d') ?? '-',
            ),
        );
    }

    protected function send(string $eventType, string $title, Incident $incident, string $detail): void
    {
        if (! (bool) config('services.line.notifications.enabled', false)) {
            $this->recordDelivery(
                incident: $incident,
                eventType: $eventType,
                status: 'skipped_disabled',
                message: 'LINE notifications are disabled.',
            );

            return;
        }

        $token = (string) config('services.line.notifications.channel_access_token', '');
        $to = (string) config('services.line.notifications.to', '');

        if ($token === '' || $to === '') {
            $this->recordDelivery(
                incident: $incident,
                eventType: $eventType,
                status: 'skipped_incomplete_config',
                message: 'LINE credentials are incomplete.',
            );

            Log::warning('LINE incident notification skipped because credentials are incomplete.', [
                'incident_id' => $incident->id,
            ]);

            return;
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('services.line.notifications.timeout', 5))
                ->post('https://api.line.me/v2/bot/message/push', [
                    'to' => $to,
                    'messages' => [[
                        'type' => 'text',
                        'text' => $this->message($title, $incident, $detail),
                    ]],
                ]);

            if ($response->failed()) {
                $this->recordDelivery(
                    incident: $incident,
                    eventType: $eventType,
                    status: 'failed',
                    httpStatus: $response->status(),
                    message: $this->summarizeResponseBody($response->body()),
                    recipient: $to,
                );

                Log::warning('LINE incident notification failed.', [
                    'incident_id' => $incident->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return;
            }

            $this->recordDelivery(
                incident: $incident,
                eventType: $eventType,
                status: 'sent',
                httpStatus: $response->status(),
                recipient: $to,
            );
        } catch (\Throwable $exception) {
            $this->recordDelivery(
                incident: $incident,
                eventType: $eventType,
                status: 'failed_exception',
                message: $exception::class.': '.$exception->getMessage(),
                recipient: $to,
            );

            Log::warning('LINE incident notification could not be delivered.', [
                'incident_id' => $incident->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    protected function message(string $title, Incident $incident, string $detail): string
    {
        return implode("\n", array_filter([
            "[Daily Ops] {$title}",
            "#{$incident->id} {$incident->title}",
            $detail,
            'เปิดดู: '.route('incidents.show', $incident),
        ]));
    }

    protected function recordDelivery(
        Incident $incident,
        string $eventType,
        string $status,
        ?int $httpStatus = null,
        ?string $message = null,
        ?string $recipient = null,
    ): void {
        NotificationDelivery::query()->create([
            'incident_id' => $incident->id,
            'channel' => 'line',
            'event_type' => $eventType,
            'recipient_type' => $this->recipientType($recipient),
            'recipient_fingerprint' => $this->recipientFingerprint($recipient),
            'status' => $status,
            'http_status' => $httpStatus,
            'message' => $message !== null ? mb_substr($message, 0, 500) : null,
            'attempted_at' => now(),
        ]);
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
