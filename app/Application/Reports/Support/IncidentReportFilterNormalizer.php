<?php

declare(strict_types=1);

namespace App\Application\Reports\Support;

use App\Application\Reports\Data\IncidentReportFilters;
use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Domain\Incidents\Enums\IncidentSubcategory;
use App\Models\Room;
use Carbon\CarbonImmutable;

class IncidentReportFilterNormalizer
{
    /**
     * @param  array<string, mixed>  $input
     * @return array{start_date:string,end_date:string,room_id:string,category:string,subcategory:string,status:string,severity:string}
     */
    public function normalize(array $input): array
    {
        $startDate = $this->normalizeDate((string) ($input['start_date'] ?? ''), CarbonImmutable::today()->subDays(29)->toDateString());
        $endDate = $this->normalizeDate((string) ($input['end_date'] ?? ''), CarbonImmutable::today()->toDateString());

        if (CarbonImmutable::parse($startDate)->greaterThan(CarbonImmutable::parse($endDate))) {
            $endDate = $startDate;
        }

        $category = $this->allowedString((string) ($input['category'] ?? ''), IncidentCategory::values());
        $subcategory = $this->allowedString(
            (string) ($input['subcategory'] ?? ''),
            IncidentSubcategory::valuesForCategory($category !== '' ? $category : null),
        );

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'room_id' => $this->normalizeRoomId($input['room_id'] ?? ''),
            'category' => $category,
            'subcategory' => $subcategory,
            'status' => $this->allowedString((string) ($input['status'] ?? ''), IncidentStatus::values()),
            'severity' => $this->allowedString((string) ($input['severity'] ?? ''), IncidentSeverity::values()),
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function filters(array $input): IncidentReportFilters
    {
        $normalized = $this->normalize($input);

        return new IncidentReportFilters(
            startDate: CarbonImmutable::parse($normalized['start_date'])->startOfDay(),
            endDate: CarbonImmutable::parse($normalized['end_date'])->endOfDay(),
            roomId: $normalized['room_id'] !== '' ? (int) $normalized['room_id'] : null,
            category: $normalized['category'],
            subcategory: $normalized['subcategory'],
            status: $normalized['status'],
            severity: $normalized['severity'],
        );
    }

    protected function normalizeDate(string $value, string $fallback): string
    {
        if (trim($value) === '') {
            return $fallback;
        }

        try {
            return CarbonImmutable::parse($value)->toDateString();
        } catch (\Throwable) {
            return $fallback;
        }
    }

    /**
     * @param  list<string>  $allowed
     */
    protected function allowedString(string $value, array $allowed): string
    {
        return in_array($value, $allowed, true) ? $value : '';
    }

    protected function normalizeRoomId(mixed $value): string
    {
        if (! is_numeric($value)) {
            return '';
        }

        $roomId = (int) $value;

        return Room::query()->whereKey($roomId)->exists() ? (string) $roomId : '';
    }
}
