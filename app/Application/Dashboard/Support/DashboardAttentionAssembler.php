<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use App\Application\Incidents\Support\IncidentStalePolicy;
use Illuminate\Support\Facades\Route;

class DashboardAttentionAssembler
{
    /**
     * @return list<array{
     *     title: string,
     *     description: string,
     *     count: int,
     *     actionLabel: string|null,
     *     url: string|null,
     *     tone: 'warning'|'danger'|'info'
     * }>
     */
    public function __invoke(
        int $todayRuns,
        int $submittedTodayRuns,
        int $completionRate,
        int $highSeverityUnresolvedCount,
        int $staleUnresolvedCount,
        int $scopeLanesMissingTemplateCount,
        int $scopeLanesIncompleteCount,
    ): array {
        $attentionItems = [];

        if ($todayRuns === 0) {
            $attentionItems[] = [
                'title' => 'No checklist runs created today',
                'description' => 'Staff have not opened today\'s checklist flow yet, so daily completion cannot be tracked.',
                'count' => 0,
                'actionLabel' => null,
                'url' => null,
                'tone' => 'warning',
            ];
        } elseif ($completionRate < 100) {
            $attentionItems[] = [
                'title' => 'Checklist completion is still in progress',
                'description' => sprintf(
                    '%d of %d checklist runs are submitted today. Follow up if the team should already be finished.',
                    $submittedTodayRuns,
                    $todayRuns,
                ),
                'count' => $todayRuns - $submittedTodayRuns,
                'actionLabel' => null,
                'url' => null,
                'tone' => 'info',
            ];
        }

        if ($scopeLanesMissingTemplateCount > 0) {
            $attentionItems[] = [
                'title' => 'Checklist coverage is missing a live scope lane',
                'description' => 'At least one operating lane still has no active template, so management cannot confirm that the full daily routine is configured.',
                'count' => $scopeLanesMissingTemplateCount,
                'actionLabel' => null,
                'url' => null,
                'tone' => 'warning',
            ];
        }

        if ($scopeLanesIncompleteCount > 0) {
            $attentionItems[] = [
                'title' => 'Scope lanes are still incomplete today',
                'description' => 'One or more live checklist lanes have not started or are still in progress, so the daily routine is not fully closed yet.',
                'count' => $scopeLanesIncompleteCount,
                'actionLabel' => null,
                'url' => null,
                'tone' => 'info',
            ];
        }

        if ($highSeverityUnresolvedCount > 0) {
            $attentionItems[] = [
                'title' => 'High severity incidents need attention',
                'description' => 'Open or in-progress incidents with high severity should be reviewed first.',
                'count' => $highSeverityUnresolvedCount,
                'actionLabel' => 'Review high severity incidents',
                'url' => $this->incidentsIndexUrl(['unresolved' => 1, 'severity' => 'High']),
                'tone' => 'danger',
            ];
        }

        if ($staleUnresolvedCount > 0) {
            $attentionItems[] = [
                'title' => 'Unresolved incidents are going stale',
                'description' => sprintf(
                    'These incidents have stayed unresolved for at least %d days and may need follow-up.',
                    IncidentStalePolicy::thresholdDays(),
                ),
                'count' => $staleUnresolvedCount,
                'actionLabel' => 'Review stale incidents',
                'url' => $this->incidentsIndexUrl(['unresolved' => 1, 'stale' => 1]),
                'tone' => 'warning',
            ];
        }

        return $attentionItems;
    }

    /**
     * @param  array<string, int|string>  $parameters
     */
    private function incidentsIndexUrl(array $parameters): ?string
    {
        return Route::has('incidents.index')
            ? route('incidents.index', $parameters)
            : null;
    }
}
