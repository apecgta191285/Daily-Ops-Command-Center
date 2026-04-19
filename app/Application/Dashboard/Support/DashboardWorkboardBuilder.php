<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use Illuminate\Support\Facades\Route;

class DashboardWorkboardBuilder
{
    /**
     * @param  list<array{
     *     scope: string,
     *     scope_key: string,
     *     template_title: ?string,
     *     state: 'unavailable'|'not_started'|'in_progress'|'submitted',
     *     total_runs: int,
     *     submitted_runs: int,
     *     completion_percentage: int
     * }>  $scopeChecklistLanes
     * @param  list<array{
     *     title: string,
     *     description: string,
     *     count: int,
     *     actionLabel: string|null,
     *     url: string|null,
     *     tone: 'warning'|'danger'|'info'
     * }>  $attentionItems
     * @return array{
     *     state: 'attention'|'calm',
     *     headline: string,
     *     body: string,
     *     pendingLaneCount: int,
     *     attentionCount: int,
     *     submittedLaneCount: int,
     *     lanes: list<array{
     *         scope: string,
     *         scope_key: string,
     *         template_title: ?string,
     *         state: 'unavailable'|'not_started'|'in_progress'|'submitted',
     *         state_label: string,
     *         summary: string,
     *         total_runs: int,
     *         submitted_runs: int,
     *         completion_percentage: int
     *     }>,
     *     actions: list<array{
     *         label: string,
     *         url: string|null,
     *         tone: 'primary'|'secondary'
     *     }>
     * }
     */
    public function __invoke(array $scopeChecklistLanes, array $attentionItems): array
    {
        $pendingLanes = collect($scopeChecklistLanes)
            ->filter(fn (array $lane): bool => $lane['state'] !== 'submitted')
            ->map(fn (array $lane): array => [
                ...$lane,
                'state_label' => $this->stateLabel($lane['state']),
                'summary' => $this->laneSummary($lane),
            ])
            ->values()
            ->all();

        $pendingLaneCount = count($pendingLanes);
        $attentionCount = count($attentionItems);
        $submittedLaneCount = collect($scopeChecklistLanes)
            ->where('state', 'submitted')
            ->count();

        $state = ($pendingLaneCount === 0 && $attentionCount === 0)
            ? 'calm'
            : 'attention';

        return [
            'state' => $state,
            'headline' => $this->headline($state, $pendingLaneCount),
            'body' => $this->body($state, $pendingLaneCount, $attentionCount),
            'pendingLaneCount' => $pendingLaneCount,
            'attentionCount' => $attentionCount,
            'submittedLaneCount' => $submittedLaneCount,
            'lanes' => $pendingLanes,
            'actions' => $this->actions($state),
        ];
    }

    private function headline(string $state, int $pendingLaneCount): string
    {
        if ($state === 'calm') {
            return 'Today is covered and currently calm';
        }

        return $pendingLaneCount > 0
            ? 'Today still has open operating lanes'
            : 'Checklist coverage is closed, but pressure remains';
    }

    private function body(string $state, int $pendingLaneCount, int $attentionCount): string
    {
        if ($state === 'calm') {
            return 'All live checklist lanes are configured and submitted. Management can use the supporting trend and history surfaces for review, not firefighting.';
        }

        if ($pendingLaneCount > 0) {
            return sprintf(
                '%d scope lane(s) still need coverage or completion today. Use this workboard to confirm where the operating day is still open before unresolved pressure turns into drift.',
                $pendingLaneCount,
            );
        }

        return sprintf(
            'Checklist coverage is already closed, but %d attention signal(s) still need management follow-up before the day is genuinely settled.',
            $attentionCount,
        );
    }

    /**
     * @return list<array{label: string, url: string|null, tone: 'primary'|'secondary'}>
     */
    private function actions(string $state): array
    {
        $actions = [];

        if ($state === 'attention') {
            $actions[] = [
                'label' => 'Review incidents',
                'url' => $this->routeOrNull('incidents.index'),
                'tone' => 'primary',
            ];
        }

        $actions[] = [
            'label' => 'Review today archive',
            'url' => $this->routeOrNull('checklists.history.index', ['runDate' => today()->toDateString()]),
            'tone' => 'secondary',
        ];

        return $actions;
    }

    private function stateLabel(string $state): string
    {
        return match ($state) {
            'unavailable' => 'Missing live template',
            'not_started' => 'Not started',
            'in_progress' => 'In progress',
            default => 'Submitted',
        };
    }

    /**
     * @param  array{
     *     scope: string,
     *     template_title: ?string,
     *     state: 'unavailable'|'not_started'|'in_progress'|'submitted',
     *     total_runs: int,
     *     submitted_runs: int,
     *     completion_percentage: int
     * }  $lane
     */
    private function laneSummary(array $lane): string
    {
        return match ($lane['state']) {
            'unavailable' => 'No active template is covering this operating lane yet, so the workday is not fully configured.',
            'not_started' => 'A live template exists, but staff have not opened this lane today yet.',
            'in_progress' => sprintf(
                '%d of %d run(s) are submitted. This lane is active, but the day is not closed here yet.',
                $lane['submitted_runs'],
                $lane['total_runs'],
            ),
            default => 'This lane is already submitted.',
        };
    }

    /**
     * @param  array<string, string>  $parameters
     */
    private function routeOrNull(string $name, array $parameters = []): ?string
    {
        return Route::has($name) ? route($name, $parameters) : null;
    }
}
