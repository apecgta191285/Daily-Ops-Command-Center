<?php

namespace App\Application\Checklists\Data;

use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;

readonly class DailyRunContext
{
    /**
     * @param  array<int, array{result: string|null, note: string|null}>  $runItems
     * @param  list<array{run_date: string, submitted_at: string, not_done_count: int, noted_items_count: int}>  $recentRuns
     */
    public function __construct(
        public ?string $errorState,
        public ?ChecklistRun $run = null,
        public ?ChecklistTemplate $template = null,
        public array $runItems = [],
        public array $recentRuns = [],
        public bool $isSubmitted = false,
    ) {}

    public function hasError(): bool
    {
        return $this->errorState !== null;
    }
}
