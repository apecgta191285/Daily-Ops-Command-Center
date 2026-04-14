<?php

namespace App\Livewire\Management\Incidents;

use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    private const STALE_INCIDENT_DAYS = 2;

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

    public array $statuses = [];

    public array $categories = [];

    public array $severities = [];

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
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $incidents = Incident::query()
            ->with('creator')
            ->when($this->unresolved, fn ($query) => $query->where('status', '!=', IncidentStatus::Resolved->value))
            ->when($this->stale, fn ($query) => $query
                ->where('status', '!=', IncidentStatus::Resolved->value)
                ->where('created_at', '<=', now()->subDays(self::STALE_INCIDENT_DAYS)))
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->category !== '', fn ($query) => $query->where('category', $this->category))
            ->when($this->severity !== '', fn ($query) => $query->where('severity', $this->severity))
            ->latest()
            ->get();

        return view('livewire.management.incidents.index', [
            'incidents' => $incidents,
        ]);
    }
}
