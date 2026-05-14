<?php

declare(strict_types=1);

namespace App\Application\Incidents\Support;

use App\Models\Incident;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalIncidentNotifier
{
    public function incidentCreated(Incident $incident): void
    {
        $this->send(
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
            title: 'อัปเดตผู้รับผิดชอบ/กำหนดติดตาม',
            incident: $incident,
            detail: sprintf(
                "ผู้รับผิดชอบ: %s\nกำหนดติดตาม: %s",
                $incident->owner?->name ?? '-',
                $incident->follow_up_due_at?->format('Y-m-d') ?? '-',
            ),
        );
    }

    protected function send(string $title, Incident $incident, string $detail): void
    {
        if (! (bool) config('services.line.notifications.enabled', false)) {
            return;
        }

        $token = (string) config('services.line.notifications.channel_access_token', '');
        $to = (string) config('services.line.notifications.to', '');

        if ($token === '' || $to === '') {
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
                Log::warning('LINE incident notification failed.', [
                    'incident_id' => $incident->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $exception) {
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
}
