<?php

namespace App\Livewire\Staff\Checklists;

use App\Models\ChecklistRun;
use App\Models\ChecklistRunItem;
use App\Models\ChecklistTemplate;
use Livewire\Attributes\Layout;
use Livewire\Component;

class DailyRun extends Component
{
    public $errorState = null; // 'zero' or 'multiple'

    public $run;

    public $template;

    public $runItems = [];

    public $isSubmitted = false;

    public function mount()
    {
        $templates = ChecklistTemplate::where('is_active', true)->get();

        if ($templates->count() === 0) {
            $this->errorState = 'zero';

            return;
        } elseif ($templates->count() > 1) {
            $this->errorState = 'multiple';

            return;
        }

        $this->template = $templates->first();

        $today = now()->format('Y-m-d 00:00:00'); // Ensure sqlite constraint matches

        $this->run = ChecklistRun::firstOrCreate([
            'checklist_template_id' => $this->template->id,
            'run_date' => $today,
            'created_by' => auth()->id(),
        ], [
            'assigned_team_or_scope' => $this->template->scope,
        ]);

        if ($this->run->wasRecentlyCreated) {
            $itemsData = $this->template->items->map(function ($item) {
                return [
                    'checklist_item_id' => $item->id,
                ];
            })->toArray();

            $this->run->items()->createMany($itemsData);
        }

        $this->isSubmitted = ! is_null($this->run->submitted_at);

        foreach ($this->run->items()->with('checklistItem')->get() as $runItem) {
            $this->runItems[$runItem->id] = [
                'result' => $runItem->result,
                'note' => $runItem->note,
            ];
        }
    }

    public function submit()
    {
        if ($this->isSubmitted) {
            return;
        }

        // Validate that all items have a result of 'Done' or 'Not Done'
        $rules = [];
        $messages = [];
        foreach ($this->runItems as $id => $data) {
            $rules["runItems.{$id}.result"] = 'required|in:Done,Not Done';
            $messages["runItems.{$id}.result.required"] = 'Please answer this item.';
            $messages["runItems.{$id}.result.in"] = 'Please answer Done or Not Done.';
        }

        $this->validate($rules, $messages);

        foreach ($this->runItems as $id => $data) {
            ChecklistRunItem::where('id', $id)->update([
                'result' => $data['result'],
                'note' => $data['note'],
                'checked_by' => auth()->id(),
                'checked_at' => now(),
            ]);
        }

        $this->run->update([
            'submitted_at' => now(),
            'submitted_by' => auth()->id(),
        ]);

        $this->isSubmitted = true;
        session()->flash('message', 'Checklist submitted successfully.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.staff.checklists.daily-run');
    }
}
