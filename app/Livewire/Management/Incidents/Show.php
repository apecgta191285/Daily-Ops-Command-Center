<?php

namespace App\Livewire\Management\Incidents;

use App\Application\Incidents\Actions\TransitionIncidentStatus;
use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Incident $incident;

    public string $status = '';

    public string $followUpNote = '';

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
            'followUpNote' => 'nullable|string|max:500',
        ]);

        $result = app(TransitionIncidentStatus::class)($this->incident, $this->status, auth()->id(), $this->followUpNote);
        $this->incident = $result->incident;

        if (! $result->changed) {
            return;
        }

        $this->followUpNote = '';
        session()->flash('message', 'Incident status updated successfully.');
    }

    public function getIsStaleProperty(): bool
    {
        return IncidentStalePolicy::isStale($this->incident->created_at, $this->incident->status);
    }

    public function getStaleThresholdDaysProperty(): int
    {
        return IncidentStalePolicy::thresholdDays();
    }

    public function getAgeInDaysProperty(): int
    {
        return (int) $this->incident->created_at->startOfDay()->diffInDays(now()->startOfDay());
    }

    public function getFollowUpNoteLabelProperty(): string
    {
        return $this->status === IncidentStatus::Resolved->value
            ? 'Resolution Summary (Optional)'
            : 'Next Action Note (Optional)';
    }

    public function getFollowUpNoteHelpProperty(): string
    {
        return $this->status === IncidentStatus::Resolved->value
            ? 'Summarize what fixed the issue or what condition now counts as resolved.'
            : 'Use this when the status changes and you want the activity timeline to show the next follow-up step.';
    }

    public function getLatestNextActionNoteProperty(): ?string
    {
        return $this->incident->activities
            ->where('action_type', 'next_action_note')
            ->sortByDesc('created_at')
            ->first()
            ?->summary;
    }

    public function getLatestResolutionNoteProperty(): ?string
    {
        return $this->incident->activities
            ->where('action_type', 'resolution_note')
            ->sortByDesc('created_at')
            ->first()
            ?->summary;
    }

    public function getActivityTypeLabel(string $actionType): string
    {
        return match ($actionType) {
            'status_changed' => 'Status update',
            'next_action_note' => 'Next action',
            'resolution_note' => 'Resolution note',
            'created' => 'Reported',
            default => str_replace('_', ' ', $actionType),
        };
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.management.incidents.show');
    }
}
