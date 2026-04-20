<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use BackedEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class DashboardHotspotAssembler
{
    /**
     * @param  Collection<int, object>  $rows
     * @return list<array{
     *     category: string,
     *     unresolvedCount: int,
     *     staleCount: int,
     *     url: string|null
     * }>
     */
    public function __invoke(Collection $rows): array
    {
        return $rows
            ->map(function (object $row): array {
                $category = $this->normalizeCategory($row->category);

                return [
                    'category' => $category,
                    'unresolvedCount' => (int) $row->unresolved_count,
                    'staleCount' => (int) $row->stale_count,
                    'url' => $this->incidentsIndexUrl([
                        'unresolved' => 1,
                        'category' => $category,
                    ]),
                ];
            })
            ->all();
    }

    private function normalizeCategory(mixed $category): string
    {
        if ($category instanceof BackedEnum) {
            return (string) $category->value;
        }

        return (string) $category;
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
