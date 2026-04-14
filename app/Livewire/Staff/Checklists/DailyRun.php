<?php

namespace App\Livewire\Staff\Checklists;

use App\Application\Checklists\Actions\InitializeDailyRun;
use App\Application\Checklists\Actions\SubmitDailyRun;
use App\Application\Checklists\Data\DailyRunContext;
use App\Domain\Checklists\Enums\ChecklistResult;
use Illuminate\Support\Arr;
use Livewire\Attributes\Layout;
use Livewire\Component;

class DailyRun extends Component
{
    public $errorState = null; // 'zero' or 'multiple'

    public $run;

    public $template;

    public $runItems = [];

    public $recentRuns = [];

    public $isSubmitted = false;

    public function mount(): void
    {
        $this->applyContext(app(InitializeDailyRun::class)(auth()->id()));
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

    public function getCompletionPercentageProperty(): int
    {
        if ($this->totalItems === 0) {
            return 0;
        }

        return (int) round(($this->answeredItems / $this->totalItems) * 100);
    }

    private function applyContext(DailyRunContext $context): void
    {
        $this->errorState = $context->errorState;
        $this->run = $context->run;
        $this->template = $context->template;
        $this->runItems = $context->runItems;
        $this->recentRuns = $context->recentRuns;
        $this->isSubmitted = $context->isSubmitted;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.staff.checklists.daily-run');
    }
}
