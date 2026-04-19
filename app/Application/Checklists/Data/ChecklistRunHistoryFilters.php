<?php

declare(strict_types=1);

namespace App\Application\Checklists\Data;

readonly class ChecklistRunHistoryFilters
{
    public function __construct(
        public string $runDate = '',
        public string $scopeRouteKey = '',
        public string $operatorId = '',
    ) {}
}
