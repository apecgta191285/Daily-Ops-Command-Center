<?php

namespace App\Livewire\Management\Incidents;

use App\Models\Incident;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    public string $status = '';

    public string $category = '';

    public string $severity = '';

    public array $statuses = [
        'Open',
        'In Progress',
        'Resolved',
    ];

    public array $categories = [
        'อุปกรณ์คอมพิวเตอร์',
        'เครือข่าย',
        'ความสะอาด',
        'ความปลอดภัย',
        'สภาพแวดล้อม',
        'อื่น ๆ',
    ];

    public array $severities = [
        'Low',
        'Medium',
        'High',
    ];

    #[Layout('layouts.app')]
    public function render()
    {
        $incidents = Incident::query()
            ->with('creator')
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->category !== '', fn ($query) => $query->where('category', $this->category))
            ->when($this->severity !== '', fn ($query) => $query->where('severity', $this->severity))
            ->latest()
            ->get();

        return view('livewire.management.incidents.index', [
            'incidents' => $incidents,
        ]);
    }
}
