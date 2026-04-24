<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Access\Enums\UserRole;
use App\Models\ChecklistRun;
use App\Models\User;

class ChecklistRunPolicy
{
    public function view(User $user, ChecklistRun $run): bool
    {
        return $this->isManagementUser($user);
    }

    protected function isManagementUser(User $user): bool
    {
        return in_array($user->role->value, UserRole::managementValues(), true);
    }
}
