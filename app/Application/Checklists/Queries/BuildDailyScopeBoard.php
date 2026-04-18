<?php

declare(strict_types=1);

namespace App\Application\Checklists\Queries;

use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;

class BuildDailyScopeBoard
{
    /**
     * @return list<array{
     *     scope: string,
     *     scope_key: string,
     *     template_title: ?string,
     *     state: 'unavailable'|'not_started'|'in_progress'|'submitted',
     *     run_date: ?string,
     *     answered_items: int,
     *     total_items: int,
     *     completion_percentage: int,
     *     submitted_at: ?string
     * }>
     */
    public function __invoke(int $userId): array
    {
        $activeTemplates = ChecklistTemplate::query()
            ->where('is_active', true)
            ->withCount('items')
            ->get()
            ->keyBy(fn (ChecklistTemplate $template) => $template->scope);

        $todayRuns = ChecklistRun::query()
            ->where('created_by', $userId)
            ->whereDate('run_date', today())
            ->with(['template', 'items'])
            ->get()
            ->keyBy(fn (ChecklistRun $run) => $run->template?->scope ?? '');

        return collect(ChecklistScope::cases())
            ->map(function (ChecklistScope $scope) use ($activeTemplates, $todayRuns): array {
                /** @var ChecklistTemplate|null $template */
                $template = $activeTemplates->get($scope->value);

                if ($template === null) {
                    return [
                        'scope' => $scope->value,
                        'scope_key' => $scope->routeKey(),
                        'template_title' => null,
                        'state' => 'unavailable',
                        'run_date' => null,
                        'answered_items' => 0,
                        'total_items' => 0,
                        'completion_percentage' => 0,
                        'submitted_at' => null,
                    ];
                }

                /** @var ChecklistRun|null $run */
                $run = $todayRuns->get($scope->value);

                if ($run === null) {
                    return [
                        'scope' => $scope->value,
                        'scope_key' => $scope->routeKey(),
                        'template_title' => $template->title,
                        'state' => 'not_started',
                        'run_date' => today()->toDateString(),
                        'answered_items' => 0,
                        'total_items' => (int) $template->items_count,
                        'completion_percentage' => 0,
                        'submitted_at' => null,
                    ];
                }

                $answeredItems = $run->items
                    ->filter(fn ($item) => filled($item->result))
                    ->count();
                $totalItems = $run->items->count();

                return [
                    'scope' => $scope->value,
                    'scope_key' => $scope->routeKey(),
                    'template_title' => $template->title,
                    'state' => $run->submitted_at !== null ? 'submitted' : 'in_progress',
                    'run_date' => $run->run_date?->toDateString(),
                    'answered_items' => $answeredItems,
                    'total_items' => $totalItems,
                    'completion_percentage' => $totalItems > 0
                        ? (int) round(($answeredItems / $totalItems) * 100)
                        : 0,
                    'submitted_at' => $run->submitted_at?->toIso8601String(),
                ];
            })
            ->values()
            ->all();
    }
}
