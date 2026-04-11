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
}
