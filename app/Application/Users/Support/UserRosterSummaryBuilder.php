<?php

declare(strict_types=1);

namespace App\Application\Users\Support;

use App\Domain\Access\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRosterSummaryBuilder
{
    /**
     * @return array{
     *     total_count: int,
     *     active_count: int,
     *     inactive_count: int,
     *     management_count: int,
     *     role_lanes: list<array{
     *         role: string,
     *         title: string,
     *         description: string,
     *         total_count: int,
     *         active_count: int,
     *         inactive_count: int,
     *         state: 'covered'|'warning'
     *     }>
     * }
     */
    public function __invoke(): array
    {
        $users = User::query()
            ->select(['role', 'is_active'])
            ->get();

        return [
            'total_count' => $users->count(),
            'active_count' => $users->where('is_active', true)->count(),
            'inactive_count' => $users->where('is_active', false)->count(),
            'management_count' => $users
                ->whereIn('role', UserRole::managementValues())
                ->count(),
            'role_lanes' => collect(UserRole::cases())
                ->map(fn (UserRole $role): array => $this->buildRoleLane($role, $users))
                ->all(),
        ];
    }

    /**
     * @param  Collection<int, User>  $users
     * @return array{
     *     role: string,
     *     title: string,
     *     description: string,
     *     total_count: int,
     *     active_count: int,
     *     inactive_count: int,
     *     state: 'covered'|'warning'
     * }
     */
    private function buildRoleLane(UserRole $role, Collection $users): array
    {
        $lane = $users->where('role', $role->value);
        $activeCount = $lane->where('is_active', true)->count();

        return [
            'role' => $role->value,
            'title' => match ($role) {
                UserRole::Admin => 'Administrators',
                UserRole::Supervisor => 'Supervisors',
                UserRole::Staff => 'Staff operators',
            },
            'description' => match ($role) {
                UserRole::Admin => 'Own platform governance, template administration, and user lifecycle decisions.',
                UserRole::Supervisor => 'Own dashboard, incident queue, and follow-up handling without admin-level control.',
                UserRole::Staff => 'Own checklist execution and incident reporting during day-to-day operations.',
            },
            'total_count' => $lane->count(),
            'active_count' => $activeCount,
            'inactive_count' => $lane->count() - $activeCount,
            'state' => $activeCount === 0 ? 'warning' : 'covered',
        ];
    }
}
