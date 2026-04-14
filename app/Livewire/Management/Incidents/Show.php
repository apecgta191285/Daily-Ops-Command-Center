<?php

namespace App\Livewire\Management\Incidents;

use App\Application\Incidents\Actions\TransitionIncidentStatus;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    private const STALE_INCIDENT_DAYS = 2;

    public Incident $incident;

    public string $status = '';

    public string $nextActionNote = '';

    public array $statuses = [];

    public function mount(Incident $incident): void
    {
        $this->statuses = IncidentStatus::values();
        $this->incident = $incident->load(['creator', 'activities.actor']);
        $this->status = $this->incident->status;
    }

    public function updateStatus(): void
    {
        $this->validate([
            'status' => 'required|in:'.implode(',', IncidentStatus::values()),
            'nextActionNote' => 'nullable|string|max:500',
        ]);

        $result = app(TransitionIncidentStatus::class)($this->incident, $this->status, auth()->id(), $this->nextActionNote);
        $this->incident = $result->incident;

        if (! $result->changed) {
            return;
        }

        $this->nextActionNote = '';
        session()->flash('message', 'Incident status updated successfully.');
    }

    public function getIsStaleProperty(): bool
    {
        return $this->incident->status !== IncidentStatus::Resolved->value
            && $this->incident->created_at->lte(now()->subDays(self::STALE_INCIDENT_DAYS));
    }

    public function getAgeInDaysProperty(): int
    {
        return (int) $this->incident->created_at->startOfDay()->diffInDays(now()->startOfDay());
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.management.incidents.show');
    }
}
