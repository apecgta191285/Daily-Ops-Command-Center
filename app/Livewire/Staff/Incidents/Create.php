<?php

namespace App\Livewire\Staff\Incidents;

use App\Models\Incident;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $title = '';

    public $category = '';

    public $severity = '';

    public $description = '';

    public $attachment;

    public $categories = [
        'อุปกรณ์คอมพิวเตอร์',
        'เครือข่าย',
        'ความสะอาด',
        'ความปลอดภัย',
        'สภาพแวดล้อม',
        'อื่น ๆ',
    ];

    public $severities = [
        'Low',
        'Medium',
        'High',
    ];

    public function submit()
    {
        $this->validate([
            'title' => 'required|string|max:120',
            'category' => 'required|string|in:'.implode(',', $this->categories),
            'severity' => 'required|string|in:'.implode(',', $this->severities),
            'description' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // Limit to 10MB
        ]);

        $attachmentPath = null;
        if ($this->attachment) {
            // Save to local public disk
            $attachmentPath = $this->attachment->store('incidents', 'public');
        }

        $incident = Incident::create([
            'title' => $this->title,
            'category' => $this->category,
            'severity' => $this->severity,
            'status' => 'Open',
            'description' => $this->description,
            'attachment_path' => $attachmentPath,
            'created_by' => auth()->id(),
        ]);

        $incident->activities()->create([
            'action_type' => 'created',
            'summary' => 'Incident reported',
            'actor_id' => auth()->id(),
        ]);

        session()->flash('message', 'Incident reported successfully.');

        $this->reset(['title', 'category', 'severity', 'description', 'attachment']);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.staff.incidents.create');
    }
}
