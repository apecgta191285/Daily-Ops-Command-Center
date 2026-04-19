<?php

declare(strict_types=1);

namespace App\Livewire\Management\Checklists;

use App\Application\Checklists\Support\ChecklistRunArchiveRecapBuilder;
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

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.management.checklists.history-show');
    }
}
