<?php

declare(strict_types=1);

namespace App\Livewire\Management\Incidents;

use App\Application\Incidents\Data\IncidentListFilters;
use App\Application\Incidents\Queries\ListIncidents;
use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(except: '')]
    public string $severity = '';

    #[Url(except: false)]
    public bool $unresolved = false;

    #[Url(except: false)]
    public bool $stale = false;

    #[Url(except: false)]
    public bool $unowned = false;

    #[Url(except: false)]
    public bool $mine = false;

    #[Url(except: false)]
    public bool $overdue = false;

    public array $statuses = [];

    public array $categories = [];

    public array $severities = [];

    protected string $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->statuses = IncidentStatus::values();
        $this->categories = IncidentCategory::values();
        $this->severities = IncidentSeverity::values();

        if (! in_array($this->status, $this->statuses, true)) {
            $this->status = '';
        }

        if (! in_array($this->category, $this->categories, true)) {
            $this->category = '';
        }

        if (! in_array($this->severity, $this->severities, true)) {
            $this->severity = '';
        }
    }

    public function clearFilters(): void
    {
        $this->status = '';
        $this->category = '';
        $this->severity = '';
        $this->unresolved = false;
        $this->stale = false;
        $this->unowned = false;
        $this->mine = false;
        $this->overdue = false;
    }

    public function updatedMine(bool $value): void
    {
        if ($value) {
            $this->unowned = false;
        }
    }

    public function updatedUnowned(bool $value): void
    {
        if ($value) {
            $this->mine = false;
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'status',
            'category',
            'severity',
            'unresolved',
            'stale',
            'unowned',
            'mine',
            'overdue',
        ], true)) {
            $this->resetPage();
        }
    }

    public function getStaleThresholdDaysProperty(): int
    {
        return IncidentStalePolicy::thresholdDays();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $incidents = app(ListIncidents::class)->paginate(new IncidentListFilters(
            status: $this->status,
            category: $this->category,
            severity: $this->severity,
            unresolved: $this->unresolved,
            stale: $this->stale,
            unowned: $this->unowned,
            mine: $this->mine,
            overdue: $this->overdue,
            actorId: auth()->id(),
        ), perPage: 15);

        return view('livewire.management.incidents.index', [
            'incidents' => $incidents,
        ]);
    }
}
