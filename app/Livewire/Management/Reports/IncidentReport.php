<?php

declare(strict_types=1);

namespace App\Livewire\Management\Reports;

use App\Application\Reports\Data\IncidentReportFilters;
use App\Application\Reports\Queries\BuildIncidentReport;
use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Domain\Incidents\Enums\IncidentSubcategory;
use App\Models\Room;
use Carbon\CarbonImmutable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class IncidentReport extends Component
{
    #[Url(except: '')]
    public string $startDate = '';

    #[Url(except: '')]
    public string $endDate = '';

    #[Url(except: '')]
    public string $roomId = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(except: '')]
    public string $subcategory = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $severity = '';

    /** @var list<array{id:string,name:string,code:string}> */
    public array $rooms = [];

    /** @var list<string> */
    public array $categories = [];

    /** @var list<string> */
    public array $subcategories = [];

    /** @var list<string> */
    public array $statuses = [];

    /** @var list<string> */
    public array $severities = [];

    public function mount(): void
    {
        $this->categories = IncidentCategory::values();
        $this->statuses = IncidentStatus::values();
        $this->severities = IncidentSeverity::values();
        $this->rooms = Room::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn (Room $room): array => [
                'id' => (string) $room->id,
                'name' => $room->name,
                'code' => $room->code,
            ])
            ->all();

        $this->startDate = $this->normalizeDate($this->startDate, CarbonImmutable::today()->subDays(29)->toDateString());
        $this->endDate = $this->normalizeDate($this->endDate, CarbonImmutable::today()->toDateString());
        $this->sanitizeFilters();
    }

    public function applyPreset(string $preset): void
    {
        $today = CarbonImmutable::today();

        match ($preset) {
            '7d' => [$this->startDate, $this->endDate] = [$today->subDays(6)->toDateString(), $today->toDateString()],
            '30d' => [$this->startDate, $this->endDate] = [$today->subDays(29)->toDateString(), $today->toDateString()],
            'month' => [$this->startDate, $this->endDate] = [$today->startOfMonth()->toDateString(), $today->toDateString()],
            default => null,
        };
    }

    public function clearFilters(): void
    {
        $this->roomId = '';
        $this->category = '';
        $this->subcategory = '';
        $this->status = '';
        $this->severity = '';
        $this->subcategories = IncidentSubcategory::valuesForCategory(null);
    }

    public function updated(string $property): void
    {
        if ($property === 'category') {
            $this->refreshSubcategoryOptions();
        }

        if (in_array($property, ['startDate', 'endDate', 'roomId', 'category', 'subcategory', 'status', 'severity'], true)) {
            $this->sanitizeFilters();
        }
    }

    public function getDateRangeLabelProperty(): string
    {
        return CarbonImmutable::parse($this->startDate)->format('d/m/Y')
            .' - '.
            CarbonImmutable::parse($this->endDate)->format('d/m/Y');
    }

    #[Layout('layouts.app')]
    public function render(BuildIncidentReport $buildIncidentReport)
    {
        return view('livewire.management.reports.incident-report', [
            'report' => $buildIncidentReport($this->filters()),
        ]);
    }

    protected function filters(): IncidentReportFilters
    {
        return new IncidentReportFilters(
            startDate: CarbonImmutable::parse($this->startDate)->startOfDay(),
            endDate: CarbonImmutable::parse($this->endDate)->endOfDay(),
            roomId: $this->roomId !== '' ? (int) $this->roomId : null,
            category: $this->category,
            subcategory: $this->subcategory,
            status: $this->status,
            severity: $this->severity,
        );
    }

    protected function sanitizeFilters(): void
    {
        $this->startDate = $this->normalizeDate($this->startDate, CarbonImmutable::today()->subDays(29)->toDateString());
        $this->endDate = $this->normalizeDate($this->endDate, CarbonImmutable::today()->toDateString());

        if (CarbonImmutable::parse($this->startDate)->greaterThan(CarbonImmutable::parse($this->endDate))) {
            $this->endDate = $this->startDate;
        }

        if ($this->roomId !== '' && ! collect($this->rooms)->pluck('id')->contains($this->roomId)) {
            $this->roomId = '';
        }

        if (! in_array($this->category, $this->categories, true)) {
            $this->category = '';
        }

        $this->refreshSubcategoryOptions();

        if (! in_array($this->status, $this->statuses, true)) {
            $this->status = '';
        }

        if (! in_array($this->severity, $this->severities, true)) {
            $this->severity = '';
        }
    }

    protected function refreshSubcategoryOptions(): void
    {
        $this->subcategories = IncidentSubcategory::valuesForCategory($this->category !== '' ? $this->category : null);

        if ($this->subcategory !== '' && ! in_array($this->subcategory, $this->subcategories, true)) {
            $this->subcategory = '';
        }
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
}
