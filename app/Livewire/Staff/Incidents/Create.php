<?php

namespace App\Livewire\Staff\Incidents;

use App\Application\Incidents\Actions\CreateIncident;
use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
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

    public array $categories = [];

    public array $severities = [];

    public function mount(): void
    {
        $this->categories = IncidentCategory::values();
        $this->severities = IncidentSeverity::values();

        if (request()->string('from')->value() === 'checklist') {
            $this->title = (string) request()->string('title')->trim()->limit(120);

            $category = request()->string('category')->value();
            if (in_array($category, $this->categories, true)) {
                $this->category = $category;
            }

            $severity = request()->string('severity')->value();
            if (in_array($severity, $this->severities, true)) {
                $this->severity = $severity;
            }

            $description = request()->string('description')->trim()->value();
            if ($description !== '') {
                $this->description = $description;
            }
        }
    }

    public function submit(): void
    {
        $this->validate([
            'title' => 'required|string|max:120',
            'category' => 'required|string|in:'.implode(',', $this->categories),
            'severity' => 'required|string|in:'.implode(',', $this->severities),
            'description' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // Limit to 10MB
        ]);

        app(CreateIncident::class)([
            'title' => $this->title,
            'category' => $this->category,
            'severity' => $this->severity,
            'description' => $this->description,
        ], auth()->id(), $this->attachment);

        session()->flash('message', 'Incident reported successfully.');

        $this->reset(['title', 'category', 'severity', 'description', 'attachment']);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.staff.incidents.create');
    }
}
