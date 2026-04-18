<?php

declare(strict_types=1);

namespace App\Livewire\Staff\Checklists;

use App\Application\Checklists\Actions\InitializeDailyRun;
use App\Application\Checklists\Actions\SubmitDailyRun;
use App\Application\Checklists\Data\DailyRunContext;
use App\Application\Checklists\Queries\BuildDailyScopeBoard;
use App\Application\Checklists\Support\ChecklistIncidentPrefillBuilder;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Checklists\Enums\ChecklistScope;
use Illuminate\Support\Arr;
use Livewire\Attributes\Layout;
use Livewire\Component;

class DailyRun extends Component
{
    public $errorState = null; // 'zero', 'scope_required', or 'scope_missing'

    public ?string $scopeRouteKey = null;

    public $run;

    public $template;

    public $runItems = [];

    public $recentRuns = [];

    public $itemAnomalyMemory = [];

    public $isSubmitted = false;

    public array $scopeBoard = [];

    public function mount(?string $scope = null): void
    {
        $this->scopeRouteKey = $scope;
        $this->loadState();
    }

    public function submit(): void
    {
        if ($this->isSubmitted) {
            return;
        }

        $rules = [];
        $messages = [];
        $allowedResults = implode(',', ChecklistResult::values());

        foreach ($this->runItems as $id => $data) {
            $rules["runItems.{$id}.result"] = "required|in:{$allowedResults}";
            $messages["runItems.{$id}.result.required"] = 'Please answer this item.';
            $messages["runItems.{$id}.result.in"] = 'Please answer Done or Not Done.';
        }

        $this->validate($rules, $messages);

        $this->run = app(SubmitDailyRun::class)($this->run, $this->runItems, auth()->id());
        $this->isSubmitted = $this->run->submitted_at !== null;
        $notDoneCount = collect($this->runItems)
            ->where('result', ChecklistResult::NotDone->value)
            ->count();

        session()->flash(
            'message',
            $notDoneCount > 0
                ? "Checklist submitted successfully. {$notDoneCount} item(s) were marked Not Done."
                : 'Checklist submitted successfully. All items were marked Done.'
        );
    }

    public function getTotalItemsProperty(): int
    {
        return count($this->runItems);
    }

    public function getAnsweredItemsProperty(): int
    {
        return collect($this->runItems)
            ->filter(fn (array $item) => filled(Arr::get($item, 'result')))
            ->count();
    }

    public function getRemainingItemsProperty(): int
    {
        return max($this->totalItems - $this->answeredItems, 0);
    }

    public function getNotDoneItemsProperty(): int
    {
        return collect($this->runItems)
            ->where('result', ChecklistResult::NotDone->value)
            ->count();
    }

    public function getNotedItemsProperty(): int
    {
        return collect($this->runItems)
            ->filter(fn (array $item) => filled(Arr::get($item, 'note')))
            ->count();
    }

    public function getCompletionPercentageProperty(): int
    {
        if ($this->totalItems === 0) {
            return 0;
        }

        return (int) round(($this->answeredItems / $this->totalItems) * 100);
    }

    public function getIncidentPrefillUrlProperty(): string
    {
        $prefill = app(ChecklistIncidentPrefillBuilder::class)
            ->fromDailyRun($this->run, $this->template, $this->runItems);

        return route('incidents.create', [
            ...$prefill->toRouteParameters(),
            'checklist_scope' => $this->scopeRouteKey,
        ]);
    }

    /**
     * @return list<string>
     */
    public function getRepeatedNotDoneTitlesProperty(): array
    {
        return collect($this->run?->items ?? [])
            ->filter(function ($runItem) {
                $currentResult = $this->runItems[$runItem->id]['result'] ?? null;
                $memory = $this->itemAnomalyMemory[$runItem->checklist_item_id] ?? null;

                return $currentResult === ChecklistResult::NotDone->value
                    && ($memory['recent_not_done_count'] ?? 0) > 0;
            })
            ->map(fn ($runItem) => $runItem->checklistItem->title)
            ->values()
            ->all();
    }

    private function applyContext(DailyRunContext $context): void
    {
        $this->errorState = $context->errorState;
        $this->run = $context->run;
        $this->template = $context->template;
        $this->runItems = $context->runItems;
        $this->recentRuns = $context->recentRuns;
        $this->itemAnomalyMemory = $context->itemAnomalyMemory;
        $this->isSubmitted = $context->isSubmitted;
    }

    public function getScopeLabelProperty(): ?string
    {
        return ChecklistScope::fromRouteKey($this->scopeRouteKey)?->value;
    }

    private function loadState(): void
    {
        $this->scopeBoard = app(BuildDailyScopeBoard::class)(auth()->id());

        $selectedScope = ChecklistScope::fromRouteKey($this->scopeRouteKey);
        $context = app(InitializeDailyRun::class)(auth()->id(), $selectedScope);

        $this->applyContext($context);

        if ($this->scopeRouteKey === null && $context->template !== null) {
            $this->scopeRouteKey = ChecklistScope::from($context->template->scope)->routeKey();
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $this->run?->loadMissing('items.checklistItem');

        return view('livewire.staff.checklists.daily-run');
    }
}
