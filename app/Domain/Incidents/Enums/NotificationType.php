<?php

declare(strict_types=1);

namespace App\Domain\Incidents\Enums;

enum NotificationType: string
{
    case Created = 'created';
    case StatusChanged = 'status_changed';
    case AccountabilityChanged = 'accountability_changed';
}
