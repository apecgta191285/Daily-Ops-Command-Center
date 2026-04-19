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
            $errors['role'][] = 'You cannot remove administrator access from your own account.';
        }

        if ($user->is($actor) && $validated['is_active'] === false) {
            $errors['is_active'][] = 'You cannot deactivate your own administrator account.';
        }

        if (
            $user->isAdmin()
            && $user->is_active
            && ($validated['role'] !== UserRole::Admin->value || $validated['is_active'] === false)
            && $this->activeAdminCountExcluding($user) === 0
        ) {
            $errors['role'][] = 'At least one active administrator must remain in the system.';
            $errors['is_active'][] = 'At least one active administrator must remain in the system.';
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
