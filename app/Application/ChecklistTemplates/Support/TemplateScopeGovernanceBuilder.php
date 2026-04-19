<?php

declare(strict_types=1);

namespace App\Application\ChecklistTemplates\Support;

use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistTemplate;

class TemplateScopeGovernanceBuilder
{
    /**
     * @return list<array{
     *     scope: string,
     *     scope_key: string,
     *     live_template_title: ?string,
     *     live_template_id: ?int,
     *     draft_count: int,
     *     template_count: int,
     *     live_run_count: int,
     *     state: 'covered'|'missing'
     * }>
     */
    public function __invoke(): array
    {
        $templates = ChecklistTemplate::query()
            ->withCount('runs')
            ->orderBy('title')
            ->get()
            ->groupBy('scope');

        return collect(ChecklistScope::cases())
            ->map(function (ChecklistScope $scope) use ($templates): array {
                $scopeTemplates = $templates->get($scope->value, collect());
                /** @var ?ChecklistTemplate $liveTemplate */
                $liveTemplate = $scopeTemplates->first(fn (ChecklistTemplate $template): bool => $template->is_active);

                return [
                    'scope' => $scope->value,
                    'scope_key' => $scope->routeKey(),
                    'live_template_title' => $liveTemplate?->title,
                    'live_template_id' => $liveTemplate?->id,
                    'draft_count' => $scopeTemplates->where('is_active', false)->count(),
                    'template_count' => $scopeTemplates->count(),
                    'live_run_count' => $liveTemplate?->runs_count ?? 0,
                    'state' => $liveTemplate ? 'covered' : 'missing',
                ];
            })
            ->values()
            ->all();
    }
}
