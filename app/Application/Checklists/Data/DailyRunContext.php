<?php

namespace App\Application\Checklists\Data;

use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;

readonly class DailyRunContext
{
    /**
     * @param  array<int, array{result: string|null, note: string|null}>  $runItems
     * @param  list<array{run_date: string, submitted_at: string, not_done_count: int, noted_items_count: int}>  $recentRuns
     * @param  array<int, array{recent_not_done_count:int, sample_run_count:int, last_not_done_at:?string, last_note:?string}>  $itemAnomalyMemory
     */
    public function __construct(
        public ?string $errorState,
        public ?ChecklistRun $run = null,
        public ?ChecklistTemplate $template = null,
        public array $runItems = [],
        public array $recentRuns = [],
        public array $itemAnomalyMemory = [],
        public bool $isSubmitted = false,
    ) {}

    public function hasError(): bool
    {
        return $this->errorState !== null;
    }
}
