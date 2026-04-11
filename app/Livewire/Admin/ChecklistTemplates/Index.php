<?php

declare(strict_types=1);

namespace App\Livewire\Admin\ChecklistTemplates;

use App\Models\ChecklistTemplate;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.checklist-templates.index', [
            'templates' => ChecklistTemplate::query()
                ->withCount('items')
                ->orderByDesc('is_active')
                ->orderBy('scope')
                ->orderBy('title')
                ->get(),
        ]);
    }
}
