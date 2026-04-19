<?php

declare(strict_types=1);

namespace App\Livewire\Management\Checklists;

use App\Application\Checklists\Data\ChecklistRunHistoryFilters;
use App\Application\Checklists\Queries\ListChecklistRunHistory;
use App\Application\Checklists\Support\ChecklistRunArchiveContextBuilder;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class HistoryIndex extends Component
{
    #[Url(except: '')]
    public string $runDate = '';

    #[Url(except: '')]
    public string $scope = '';

    #[Url(except: '')]
    public string $operator = '';

    public array $scopeOptions = [];

    public function mount(): void
    {
        $this->scopeOptions = collect(ChecklistScope::cases())
            ->map(fn (ChecklistScope $scope) => [
                'route_key' => $scope->routeKey(),
                'label' => $scope->value,
            ])
            ->all();

        if (! collect($this->scopeOptions)->pluck('route_key')->contains($this->scope)) {
            $this->scope = '';
        }

        if ($this->operator !== '' && ! User::query()->whereKey($this->operator)->exists()) {
            $this->operator = '';
        }
    }

    public function clearFilters(): void
    {
        $this->runDate = '';
        $this->scope = '';
        $this->operator = '';
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $runs = app(ListChecklistRunHistory::class)(new ChecklistRunHistoryFilters(
            runDate: $this->runDate,
            scopeRouteKey: $this->scope,
            operatorId: $this->operator,
        ));

        return view('livewire.management.checklists.history-index', [
            'runs' => $runs,
            'operators' => User::query()
                ->where('role', 'staff')
                ->orderBy('name')
                ->get(['id', 'name']),
            'archiveContext' => app(ChecklistRunArchiveContextBuilder::class)(
                $runs,
                $this->runDate !== '' ? $this->runDate : null,
            ),
        ]);
    }
}
