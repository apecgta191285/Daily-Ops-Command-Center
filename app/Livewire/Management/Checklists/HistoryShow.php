<?php

declare(strict_types=1);

namespace App\Livewire\Management\Checklists;

use App\Application\Checklists\Support\ChecklistRunArchiveRecapBuilder;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistRun;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class HistoryShow extends Component
{
    public ChecklistRun $run;

    public array $recap = [];

    public function mount(ChecklistRun $run): void
    {
        abort_if($run->submitted_at === null, 404);

        $run->loadMissing([
            'template',
            'creator',
            'submitter',
            'items.checklistItem',
            'items.checker',
        ]);

        $this->run = $run;
        $this->recap = app(ChecklistRunArchiveRecapBuilder::class)($run);
    }

    public function getScopeLabelProperty(): string
    {
        return $this->run->assigned_team_or_scope ?: ($this->run->template?->scope ?? 'Unknown scope');
    }

    public function getSubmittedByLabelProperty(): string
    {
        return $this->run->submitter?->name ?? $this->run->creator?->name ?? 'Unknown';
    }

    public function getPageTitleProperty(): string
    {
        return Str::of($this->run->template?->title ?? 'Checklist run')
            ->append(' recap')
            ->toString();
    }

    public function getDateArchiveUrlProperty(): string
    {
        return route('checklists.history.index', [
            'runDate' => $this->run->run_date->toDateString(),
        ]);
    }

    public function getScopeArchiveUrlProperty(): string
    {
        $scope = ChecklistScope::tryFrom($this->scopeLabel);

        return route('checklists.history.index', [
            'runDate' => $this->run->run_date->toDateString(),
            'scope' => $scope?->routeKey(),
        ]);
    }

    public function getOperatorArchiveUrlProperty(): string
    {
        return route('checklists.history.index', [
            'operator' => (string) $this->run->created_by,
        ]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.management.checklists.history-show');
    }
}
