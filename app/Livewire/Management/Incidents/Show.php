<?php

namespace App\Livewire\Management\Incidents;

use App\Models\Incident;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Incident $incident;

    public string $status = '';

    public array $statuses = [
        'Open',
        'In Progress',
        'Resolved',
    ];

    public function mount(Incident $incident): void
    {
        $this->incident = $incident->load(['creator', 'activities.actor']);
        $this->status = $this->incident->status;
    }

    public function updateStatus(): void
    {
        $this->validate([
            'status' => 'required|in:Open,In Progress,Resolved',
        ]);

        $previousStatus = $this->incident->status;

        if ($this->status === $previousStatus) {
            return;
        }

        $this->incident->update([
            'status' => $this->status,
            'resolved_at' => $this->status === 'Resolved' ? now() : null,
        ]);

        $this->incident->activities()->create([
            'action_type' => 'status_changed',
            'summary' => "Status changed from {$previousStatus} to {$this->status}",
            'actor_id' => auth()->id(),
            'created_at' => now(),
        ]);

        $this->incident->refresh()->load(['creator', 'activities.actor']);
        session()->flash('message', 'Incident status updated successfully.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.management.incidents.show');
    }
}
