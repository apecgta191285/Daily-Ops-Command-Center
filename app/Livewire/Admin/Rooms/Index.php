<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Rooms;

use App\Application\Rooms\Support\RoomLifecycleSummaryBuilder;
use App\Models\Room;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.rooms.index', [
            'rooms' => Room::query()
                ->withCount(['checklistRuns', 'incidents'])
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->get(),
            'roomSummary' => app(RoomLifecycleSummaryBuilder::class)(),
        ]);
    }
}
