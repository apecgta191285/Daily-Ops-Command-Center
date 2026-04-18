<?php

declare(strict_types=1);

namespace App\Domain\Checklists\Enums;

enum ChecklistScope: string
{
    case OPENING = 'เปิดห้อง';
    case MIDDAY = 'ตรวจระหว่างวัน';
    case CLOSING = 'ปิดห้อง';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function routeKey(): string
    {
        return match ($this) {
            self::OPENING => 'opening',
            self::MIDDAY => 'midday',
            self::CLOSING => 'closing',
        };
    }

    public static function fromRouteKey(?string $routeKey): ?self
    {
        return match ($routeKey) {
            'opening' => self::OPENING,
            'midday' => self::MIDDAY,
            'closing' => self::CLOSING,
            default => null,
        };
    }
}
