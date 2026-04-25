<?php

declare(strict_types=1);

namespace App\Application\Users\Support;

use App\Domain\Access\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserLifecycleGuardRail
{
    /**
     * @param  array{name: string, email: string, role: string, is_active: bool, password?: string}  $validated
     */
    public function enforce(User $user, array $validated, User $actor): void
    {
        $errors = [];

        if ($user->is($actor) && $validated['role'] !== UserRole::Admin->value) {
            $errors['role'][] = 'คุณไม่สามารถถอดสิทธิ์ผู้ดูแลระบบออกจากบัญชีของตนเองได้';
        }

        if ($user->is($actor) && $validated['is_active'] === false) {
            $errors['is_active'][] = 'คุณไม่สามารถปิดการใช้งานบัญชีผู้ดูแลระบบของตนเองได้';
        }

        if (
            $user->isAdmin()
            && $user->is_active
            && ($validated['role'] !== UserRole::Admin->value || $validated['is_active'] === false)
            && $this->activeAdminCountExcluding($user) === 0
        ) {
            $errors['role'][] = 'ต้องมีผู้ดูแลระบบที่เปิดใช้งานอย่างน้อยหนึ่งบัญชีในระบบเสมอ';
            $errors['is_active'][] = 'ต้องมีผู้ดูแลระบบที่เปิดใช้งานอย่างน้อยหนึ่งบัญชีในระบบเสมอ';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function activeAdminCountExcluding(User $user): int
    {
        return User::query()
            ->where('role', UserRole::Admin->value)
            ->where('is_active', true)
            ->whereKeyNot($user->getKey())
            ->count();
    }
}
