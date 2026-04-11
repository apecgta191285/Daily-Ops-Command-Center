<?php

namespace App\Livewire\Management\Incidents;

use App\Application\Incidents\Actions\TransitionIncidentStatus;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Incident $incident;

    public string $status = '';

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
        ]);

        $result = app(TransitionIncidentStatus::class)($this->incident, $this->status, auth()->id());
        $this->incident = $result->incident;

        if (! $result->changed) {
            return;
        }

        session()->flash('message', 'Incident status updated successfully.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.management.incidents.show');
    }
}
