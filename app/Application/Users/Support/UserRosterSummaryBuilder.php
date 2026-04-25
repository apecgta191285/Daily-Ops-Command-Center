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
                ->filter(fn (User $user): bool => in_array($user->role, [UserRole::Admin, UserRole::Supervisor], true))
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
        $lane = $users->filter(fn (User $user): bool => $user->role === $role);
        $activeCount = $lane->where('is_active', true)->count();

        return [
            'role' => $role->value,
            'title' => match ($role) {
                UserRole::Admin => 'ผู้ดูแลระบบ',
                UserRole::Supervisor => 'ผู้ดูแลห้องแล็บ',
                UserRole::Staff => 'ผู้ตรวจห้อง',
            },
            'description' => match ($role) {
                UserRole::Admin => 'ดูแลการตั้งค่าหลักของระบบ แม่แบบรายการตรวจ และการจัดการวงจรชีวิตผู้ใช้งาน',
                UserRole::Supervisor => 'ดูแลแดชบอร์ดภาพรวม คิวปัญหา และการติดตามงาน โดยไม่เปิดสิทธิ์ระดับผู้ดูแลระบบ',
                UserRole::Staff => 'ดูแลการทำรายการตรวจเช็กและแจ้งรายงานปัญหาในงานประจำวัน',
            },
            'total_count' => $lane->count(),
            'active_count' => $activeCount,
            'inactive_count' => $lane->count() - $activeCount,
            'state' => $activeCount === 0 ? 'warning' : 'covered',
        ];
    }
}
