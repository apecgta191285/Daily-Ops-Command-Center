<?php

declare(strict_types=1);

namespace App\Domain\Checklists\Enums;

enum ChecklistResult: string
{
    case Done = 'Done';
    case NotDone = 'Not Done';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
