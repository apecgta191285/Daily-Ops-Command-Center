<?php

declare(strict_types=1);

namespace App\Application\Rooms\Support;

use App\Models\Room;

class RoomLifecycleSummaryBuilder
{
    /**
     * @return array{
     *     total_count: int,
     *     active_count: int,
     *     inactive_count: int,
     *     protected_count: int,
     *     unused_count: int
     * }
     */
    public function __invoke(): array
    {
        $rooms = Room::query()
            ->withCount(['checklistRuns', 'incidents'])
            ->get();

        return [
            'total_count' => $rooms->count(),
            'active_count' => $rooms->where('is_active', true)->count(),
            'inactive_count' => $rooms->where('is_active', false)->count(),
            'protected_count' => $rooms
                ->filter(fn (Room $room): bool => ($room->checklist_runs_count + $room->incidents_count) > 0)
                ->count(),
            'unused_count' => $rooms
                ->filter(fn (Room $room): bool => ($room->checklist_runs_count + $room->incidents_count) === 0)
                ->count(),
        ];
    }
}
