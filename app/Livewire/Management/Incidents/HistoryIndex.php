<?php

declare(strict_types=1);

namespace App\Livewire\Management\Incidents;

use App\Application\Incidents\Queries\ListIncidentHistorySlices;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class HistoryIndex extends Component
{
    #[Url(except: 7)]
    public int $days = 7;

    /**
     * @var list<int>
     */
    public array $allowedDayRanges = [7, 14, 30];

    public function mount(): void
    {
        if (! in_array($this->days, $this->allowedDayRanges, true)) {
            $this->days = 7;
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.management.incidents.history-index', [
            'history' => app(ListIncidentHistorySlices::class)($this->days),
        ]);
    }
}
