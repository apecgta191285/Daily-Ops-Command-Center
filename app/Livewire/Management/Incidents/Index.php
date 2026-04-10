<?php

namespace App\Livewire\Management\Incidents;

use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    public string $status = '';

    public string $category = '';

    public string $severity = '';

    public array $statuses = [];

    public array $categories = [];

    public array $severities = [];

    public function mount(): void
    {
        $this->statuses = IncidentStatus::values();
        $this->categories = IncidentCategory::values();
        $this->severities = IncidentSeverity::values();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $incidents = Incident::query()
            ->with('creator')
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
