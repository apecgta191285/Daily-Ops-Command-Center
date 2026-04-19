<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use Illuminate\Support\Facades\Route;

class DashboardOwnershipPressureBuilder
{
    /**
     * @return array{
     *     unownedCount: int,
     *     overdueCount: int,
     *     ownedByActorCount: int,
     *     actions: list<array{
     *         label: string,
     *         count: int,
     *         url: string|null,
     *         tone: 'warning'|'danger'|'info'
     *     }>
     * }
     */
    public function __invoke(int $unownedCount, int $overdueCount, int $ownedByActorCount): array
    {
        $actions = [];

        if ($unownedCount > 0) {
            $actions[] = [
                'label' => 'Review unowned incidents',
                'count' => $unownedCount,
                'url' => $this->incidentsIndexUrl(['unowned' => 1]),
                'tone' => 'warning',
            ];
        }

        if ($overdueCount > 0) {
            $actions[] = [
                'label' => 'Review overdue follow-up',
                'count' => $overdueCount,
                'url' => $this->incidentsIndexUrl(['overdue' => 1]),
                'tone' => 'danger',
            ];
        }

        if ($ownedByActorCount > 0) {
            $actions[] = [
                'label' => 'Review incidents you own',
                'count' => $ownedByActorCount,
                'url' => $this->incidentsIndexUrl(['mine' => 1]),
                'tone' => 'info',
            ];
        }

        return [
            'unownedCount' => $unownedCount,
            'overdueCount' => $overdueCount,
            'ownedByActorCount' => $ownedByActorCount,
            'actions' => $actions,
        ];
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
