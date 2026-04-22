<?php

declare(strict_types=1);

namespace App\Application\Checklists\Actions;

use App\Application\Checklists\Data\DailyRunContext;
use App\Application\Checklists\Support\ChecklistAnomalyMemoryBuilder;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;
use App\Models\Room;

class InitializeDailyRun
{
    public function __construct(
        private readonly ChecklistAnomalyMemoryBuilder $anomalyMemoryBuilder,
    ) {}

    public function __invoke(int $userId, ?ChecklistScope $scope = null, ?int $roomId = null): DailyRunContext
    {
        $activeRooms = Room::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id']);

        if ($activeRooms->isEmpty()) {
            return new DailyRunContext(errorState: 'room_missing');
        }

        if ($roomId !== null && ! $activeRooms->pluck('id')->contains($roomId)) {
            return new DailyRunContext(errorState: 'room_required');
        }

        if ($roomId === null) {
            if ($activeRooms->count() > 1) {
                return new DailyRunContext(errorState: 'room_required');
            }

            $roomId = (int) $activeRooms->first()->id;
        }

        $templates = ChecklistTemplate::query()
            ->where('is_active', true)
            ->with('items')
            ->get();

        if ($templates->count() === 0) {
            return new DailyRunContext(errorState: 'zero');
        }

        if ($scope === null) {
            if ($templates->count() > 1) {
                return new DailyRunContext(errorState: 'scope_required');
            }

            $scope = $templates->first()?->scope;
        }

        if ($scope === null) {
            return new DailyRunContext(errorState: 'zero');
        }

        /** @var ChecklistTemplate|null $template */
        $template = $templates->first(fn (ChecklistTemplate $template): bool => $template->scope === $scope);

        if ($template === null) {
            return new DailyRunContext(errorState: 'scope_missing');
        }

        $today = now()->format('Y-m-d 00:00:00');

        $run = ChecklistRun::query()->firstOrCreate(
            [
                'checklist_template_id' => $template->id,
                'room_id' => $roomId,
                'run_date' => $today,
                'created_by' => $userId,
            ],
            [
                'assigned_team_or_scope' => $template->scope->value,
            ],
        );

        if ($run->wasRecentlyCreated) {
            $run->items()->createMany(
                $template->items->map(fn ($item) => [
                    'checklist_item_id' => $item->id,
                ])->all(),
            );
        }

        $run->load('items.checklistItem');

        $itemAnomalyMemory = $this->anomalyMemoryBuilder->forUserAndTemplate(
            userId: $userId,
            templateId: $template->id,
            excludeRunId: $run->id,
        );

        $recentRuns = ChecklistRun::query()
            ->where('created_by', $userId)
            ->where('assigned_team_or_scope', $scope->value)
            ->where('room_id', $roomId)
            ->where('submitted_at', '!=', null)
            ->whereKeyNot($run->id)
            ->with('items')
            ->latest('run_date')
            ->limit(3)
            ->get()
            ->map(fn (ChecklistRun $recentRun) => [
                'run_date' => $recentRun->run_date->toDateString(),
                'submitted_at' => $recentRun->submitted_at?->toIso8601String() ?? '',
                'not_done_count' => $recentRun->items->where('result', 'Not Done')->count(),
                'noted_items_count' => $recentRun->items->filter(fn ($item) => filled($item->note))->count(),
            ])
            ->values()
            ->all();

        return new DailyRunContext(
            errorState: null,
            run: $run,
            template: $template,
            runItems: $run->items
                ->mapWithKeys(fn ($runItem) => [
                    $runItem->id => [
                        'result' => $runItem->result,
                        'note' => $runItem->note,
                    ],
                ])
                ->all(),
            recentRuns: $recentRuns,
            itemAnomalyMemory: $itemAnomalyMemory,
            isSubmitted: $run->submitted_at !== null,
        );
    }
}
