<?php

namespace App\Domain\Incidents\Enums;

enum IncidentCategory: string
{
    case ComputerEquipment = 'อุปกรณ์คอมพิวเตอร์';
    case Network = 'เครือข่าย';
    case Cleanliness = 'ความสะอาด';
    case Safety = 'ความปลอดภัย';
    case Environment = 'สภาพแวดล้อม';
    case Other = 'อื่น ๆ';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
