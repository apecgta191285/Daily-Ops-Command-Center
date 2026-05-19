<?php

declare(strict_types=1);

namespace App\Application\Notifications\Support;

class LineNotificationRecipientResolver
{
    /**
     * @return list<string>
     */
    public function forEvent(string $eventType): array
    {
        $recipients = $this->defaultRecipients();

        foreach ($this->audienceKeys($eventType) as $key) {
            $recipients = array_merge($recipients, $this->recipientsFromConfig("services.line.notifications.{$key}_to"));
        }

        return $this->unique($recipients);
    }

    /**
     * @return list<string>
     */
    public function defaultRecipients(): array
    {
        return $this->recipientsFromConfig('services.line.notifications.to');
    }

    /**
     * @return list<string>
     */
    protected function audienceKeys(string $eventType): array
    {
        return match ($eventType) {
            'incident_created' => ['admin', 'supervisor'],
            'incident_status_changed', 'incident_accountability_changed', 'manual_redelivery' => ['admin', 'supervisor', 'staff'],
            default => [],
        };
    }

    /**
     * @return list<string>
     */
    protected function recipientsFromConfig(string $key): array
    {
        $value = config($key);

        if (is_array($value)) {
            return array_values(array_filter(
                array_map(fn (mixed $recipient): string => trim((string) $recipient), $value),
                fn (string $recipient): bool => $recipient !== '',
            ));
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $value)),
            fn (string $recipient): bool => $recipient !== '',
        ));
    }

    /**
     * @param  list<string>  $recipients
     * @return list<string>
     */
    protected function unique(array $recipients): array
    {
        $seen = [];
        $unique = [];

        foreach ($recipients as $recipient) {
            if (isset($seen[$recipient])) {
                continue;
            }

            $seen[$recipient] = true;
            $unique[] = $recipient;
        }

        return $unique;
    }
}
