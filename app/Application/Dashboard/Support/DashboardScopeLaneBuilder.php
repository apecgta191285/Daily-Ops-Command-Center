<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;

class DashboardScopeLaneBuilder
{
    /**
     * @return list<array{
     *     scope: string,
     *     scope_key: string,
     *     template_title: ?string,
     *     state: 'unavailable'|'not_started'|'in_progress'|'submitted',
     *     total_runs: int,
     *     submitted_runs: int,
     *     completion_percentage: int
     * }>
     */
    public function __invoke(): array
    {
        $today = today()->toDateString();
        $tomorrow = today()->addDay()->toDateString();

        $activeTemplates = ChecklistTemplate::query()
            ->where('is_active', true)
            ->get()
            ->keyBy('scope');

        $todayRunRows = ChecklistRun::query()
            ->selectRaw('assigned_team_or_scope as scope')
            ->selectRaw('COUNT(*) as total_runs')
            ->selectRaw('SUM(CASE WHEN submitted_at IS NOT NULL THEN 1 ELSE 0 END) as submitted_runs')
            ->where('run_date', '>=', $today)
            ->where('run_date', '<', $tomorrow)
            ->groupBy('assigned_team_or_scope')
            ->get()
            ->keyBy('scope');

        return collect(ChecklistScope::cases())
            ->map(function (ChecklistScope $scope) use ($activeTemplates, $todayRunRows): array {
                /** @var ChecklistTemplate|null $template */
                $template = $activeTemplates->get($scope->value);

                if ($template === null) {
                    return [
                        'scope' => $scope->value,
                        'scope_key' => $scope->routeKey(),
                        'template_title' => null,
                        'state' => 'unavailable',
                        'total_runs' => 0,
                        'submitted_runs' => 0,
                        'completion_percentage' => 0,
                    ];
                }

                $row = $todayRunRows->get($scope->value);
                $totalRuns = (int) ($row->total_runs ?? 0);
                $submittedRuns = (int) ($row->submitted_runs ?? 0);

                return [
                    'scope' => $scope->value,
                    'scope_key' => $scope->routeKey(),
                    'template_title' => $template->title,
                    'state' => match (true) {
                        $totalRuns === 0 => 'not_started',
                        $submittedRuns >= $totalRuns => 'submitted',
                        default => 'in_progress',
                    },
                    'total_runs' => $totalRuns,
                    'submitted_runs' => $submittedRuns,
                    'completion_percentage' => $totalRuns > 0
                        ? (int) round(($submittedRuns / $totalRuns) * 100)
                        : 0,
                ];
            })
            ->values()
            ->all();
    }
}
