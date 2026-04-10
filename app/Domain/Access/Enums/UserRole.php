<?php

namespace App\Domain\Access\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Supervisor = 'supervisor';
    case Staff = 'staff';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
