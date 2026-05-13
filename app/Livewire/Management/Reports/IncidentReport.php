<?php

declare(strict_types=1);

namespace App\Livewire\Management\Reports;

use App\Application\Reports\Data\IncidentReportFilters;
use App\Application\Reports\Queries\BuildIncidentReport;
use App\Application\Reports\Support\IncidentReportFilterNormalizer;
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

    public function getExportUrlProperty(): string
    {
        return route('reports.incidents.export', $this->filterInput());
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
        return app(IncidentReportFilterNormalizer::class)->filters($this->filterInput());
    }

    protected function sanitizeFilters(): void
    {
        $normalized = app(IncidentReportFilterNormalizer::class)->normalize($this->filterInput());

        $this->startDate = $normalized['start_date'];
        $this->endDate = $normalized['end_date'];
        $this->roomId = $normalized['room_id'];
        $this->category = $normalized['category'];
        $this->subcategory = $normalized['subcategory'];
        $this->status = $normalized['status'];
        $this->severity = $normalized['severity'];
        $this->refreshSubcategoryOptions();
    }

    protected function refreshSubcategoryOptions(): void
    {
        $this->subcategories = IncidentSubcategory::valuesForCategory($this->category !== '' ? $this->category : null);

        if ($this->subcategory !== '' && ! in_array($this->subcategory, $this->subcategories, true)) {
            $this->subcategory = '';
        }
    }

    /**
     * @return array{start_date:string,end_date:string,room_id:string,category:string,subcategory:string,status:string,severity:string}
     */
    protected function filterInput(): array
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'room_id' => $this->roomId,
            'category' => $this->category,
            'subcategory' => $this->subcategory,
            'status' => $this->status,
            'severity' => $this->severity,
        ];
    }
}
