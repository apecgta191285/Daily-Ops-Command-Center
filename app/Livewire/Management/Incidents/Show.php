<?php

declare(strict_types=1);

namespace App\Livewire\Management\Incidents;

use App\Application\Incidents\Actions\TransitionIncidentStatus;
use App\Application\Incidents\Actions\UpdateIncidentAccountability;
use App\Application\Incidents\Support\IncidentFollowUpPolicy;
use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Access\Enums\UserRole;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Incident $incident;

    public string $status = '';

    public string $followUpNote = '';

    public string $ownerId = '';

    public string $followUpDueAt = '';

    public array $statuses = [];

    public array $managementOwners = [];

    public function mount(Incident $incident): void
    {
        $this->statuses = IncidentStatus::values();
        $this->incident = $incident->load(['creator', 'owner', 'activities.actor']);
        $this->status = $this->incident->status->value;
        $this->ownerId = (string) ($this->incident->owner_id ?? '');
        $this->followUpDueAt = $this->incident->follow_up_due_at?->toDateString() ?? '';
        $this->managementOwners = User::query()
            ->whereIn('role', UserRole::managementValues())
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'role'])
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role->value,
            ])
            ->all();
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

    public function updateAccountability(): void
    {
        $payload = [
            'ownerId' => filled($this->ownerId) ? $this->ownerId : null,
            'followUpDueAt' => filled($this->followUpDueAt) ? $this->followUpDueAt : null,
        ];

        $validated = Validator::make($payload, [
            'ownerId' => 'nullable|integer|exists:users,id',
            'followUpDueAt' => 'nullable|date',
        ])->validate();

        $result = app(UpdateIncidentAccountability::class)(
            $this->incident,
            isset($validated['ownerId']) ? (int) $validated['ownerId'] : null,
            $validated['followUpDueAt'] ?? null,
            auth()->id(),
        );

        $this->incident = $result->incident;
        $this->ownerId = (string) ($this->incident->owner_id ?? '');
        $this->followUpDueAt = $this->incident->follow_up_due_at?->toDateString() ?? '';

        if (! $result->changed) {
            return;
        }

        session()->flash('message', 'Incident accountability updated successfully.');
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

    public function getIsFollowUpOverdueProperty(): bool
    {
        return IncidentFollowUpPolicy::isOverdue($this->incident->follow_up_due_at, $this->incident->status);
    }

    public function getNeedsOwnerProperty(): bool
    {
        return $this->incident->status !== IncidentStatus::Resolved
            && $this->incident->owner_id === null;
    }

    public function getActivityTypeLabel(string $actionType): string
    {
        return match ($actionType) {
            'status_changed' => 'Status update',
            'owner_changed' => 'Ownership update',
            'follow_up_due_at_changed' => 'Follow-up target',
            'next_action_note' => 'Next action',
            'resolution_note' => 'Resolution note',
            'created' => 'Reported',
            default => str_replace('_', ' ', $actionType),
        };
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $this->incident->loadMissing(['creator', 'owner', 'activities.actor']);

        return view('livewire.management.incidents.show');
    }
}
