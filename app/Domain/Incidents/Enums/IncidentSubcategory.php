<?php

declare(strict_types=1);

namespace App\Domain\Incidents\Enums;

enum IncidentSubcategory: string
{
    case ComputerDesktop = 'คอมพิวเตอร์ตั้งโต๊ะ';
    case ComputerPeripheral = 'อุปกรณ์ต่อพ่วง';
    case ComputerPrinter = 'เครื่องพิมพ์';
    case ComputerProjectorDisplay = 'โปรเจกเตอร์/จอแสดงผล';
    case ComputerPowerPoint = 'จุดไฟ/ปลั๊ก/รางปลั๊ก';

    case NetworkInternet = 'อินเทอร์เน็ต';
    case NetworkLanWifi = 'LAN/Wi-Fi';
    case NetworkSwitchRouter = 'สวิตช์/เราเตอร์';
    case NetworkService = 'ระบบเครือข่าย/บริการกลาง';

    case CleanlinessFloorDesk = 'พื้น/โต๊ะ/ทางเดิน';
    case CleanlinessTrash = 'ขยะ/ของตกค้าง';
    case CleanlinessDust = 'ฝุ่น/คราบสกปรก';

    case SafetyElectrical = 'ความเสี่ยงไฟฟ้า';
    case SafetyObstruction = 'สิ่งกีดขวาง/ทางหนีไฟ';
    case SafetyDamagedEquipment = 'อุปกรณ์ชำรุดเสี่ยงอันตราย';
    case SafetyEmergency = 'เหตุฉุกเฉินด้านความปลอดภัย';

    case EnvironmentAirConditioning = 'เครื่องปรับอากาศ/อุณหภูมิ';
    case EnvironmentLighting = 'แสงสว่าง';
    case EnvironmentNoise = 'เสียงรบกวน';
    case EnvironmentFurniture = 'โต๊ะ/เก้าอี้/เฟอร์นิเจอร์';

    case OtherRequest = 'คำขอ/ประสานงานเพิ่มเติม';
    case OtherUnclassified = 'อื่น ๆ ที่ยังไม่เข้าหมวด';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function category(): IncidentCategory
    {
        return match ($this) {
            self::ComputerDesktop,
            self::ComputerPeripheral,
            self::ComputerPrinter,
            self::ComputerProjectorDisplay,
            self::ComputerPowerPoint => IncidentCategory::ComputerEquipment,

            self::NetworkInternet,
            self::NetworkLanWifi,
            self::NetworkSwitchRouter,
            self::NetworkService => IncidentCategory::Network,

            self::CleanlinessFloorDesk,
            self::CleanlinessTrash,
            self::CleanlinessDust => IncidentCategory::Cleanliness,

            self::SafetyElectrical,
            self::SafetyObstruction,
            self::SafetyDamagedEquipment,
            self::SafetyEmergency => IncidentCategory::Safety,

            self::EnvironmentAirConditioning,
            self::EnvironmentLighting,
            self::EnvironmentNoise,
            self::EnvironmentFurniture => IncidentCategory::Environment,

            self::OtherRequest,
            self::OtherUnclassified => IncidentCategory::Other,
        };
    }

    /**
     * @return list<string>
     */
    public static function valuesForCategory(IncidentCategory|string|null $category): array
    {
        $category = $category instanceof IncidentCategory
            ? $category
            : IncidentCategory::tryFrom((string) $category);

        if ($category === null) {
            return self::values();
        }

        return array_values(array_map(
            fn (self $subcategory): string => $subcategory->value,
            array_filter(self::cases(), fn (self $subcategory): bool => $subcategory->category() === $category),
        ));
    }

    public static function isValidForCategory(string $subcategory, IncidentCategory|string|null $category): bool
    {
        return in_array($subcategory, self::valuesForCategory($category), true);
    }
}
