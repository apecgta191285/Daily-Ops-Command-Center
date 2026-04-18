<?php

declare(strict_types=1);

namespace App\Domain\Incidents\Enums;

enum IncidentStatus: string
{
    case Open = 'Open';
    case InProgress = 'In Progress';
    case Resolved = 'Resolved';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
