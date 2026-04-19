<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use Illuminate\Support\Facades\Route;

class DashboardOwnershipBucketBuilder
{
    /**
     * @return array{
     *     state: 'active'|'calm',
     *     headline: string,
     *     body: string,
     *     buckets: list<array{
     *         key: 'overdue'|'unowned'|'mine',
     *         title: string,
     *         count: int,
     *         description: string,
     *         action_label: string,
     *         url: string|null,
     *         tone: 'danger'|'warning'|'info'
     *     }>
     * }
     */
    public function __invoke(int $unownedCount, int $overdueCount, int $ownedByActorCount): array
    {
        $state = ($unownedCount === 0 && $overdueCount === 0 && $ownedByActorCount === 0)
            ? 'calm'
            : 'active';

        return [
            'state' => $state,
            'headline' => $this->headline($unownedCount, $overdueCount, $ownedByActorCount),
            'body' => $this->body($state),
            'buckets' => [
                [
                    'key' => 'overdue',
                    'title' => 'Overdue follow-up',
                    'count' => $overdueCount,
                    'description' => 'Unresolved incidents already past their target review date.',
                    'action_label' => 'Review overdue follow-up',
                    'url' => $this->incidentsIndexUrl(['overdue' => 1]),
                    'tone' => 'danger',
                ],
                [
                    'key' => 'unowned',
                    'title' => 'Unowned incidents',
                    'count' => $unownedCount,
                    'description' => 'Unresolved incidents still waiting for a clear management owner.',
                    'action_label' => 'Review unowned incidents',
                    'url' => $this->incidentsIndexUrl(['unowned' => 1]),
                    'tone' => 'warning',
                ],
                [
                    'key' => 'mine',
                    'title' => 'Owned by you',
                    'count' => $ownedByActorCount,
                    'description' => 'Unresolved incidents currently sitting in your operating bucket.',
                    'action_label' => 'Review incidents you own',
                    'url' => $this->incidentsIndexUrl(['mine' => 1]),
                    'tone' => 'info',
                ],
            ],
        ];
    }

    private function headline(int $unownedCount, int $overdueCount, int $ownedByActorCount): string
    {
        return match (true) {
            $overdueCount > 0 => 'Follow-up has started slipping past target',
            $unownedCount > 0 => 'Some incidents still do not have a clear owner',
            $ownedByActorCount > 0 => 'Your current incident bucket still needs review',
            default => 'Ownership pressure is currently under control',
        };
    }

    private function body(string $state): string
    {
        if ($state === 'calm') {
            return 'No unresolved incidents are unowned, overdue, or sitting with you right now. Management can stay in review mode instead of active queue recovery.';
        }

        return 'Use these buckets to decide whether the next move is assigning ownership, clearing overdue follow-up, or closing work already sitting with you.';
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
