<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Access\Enums\UserRole;
use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function view(User $user, Incident $incident): bool
    {
        return $this->isManagementUser($user);
    }

    public function update(User $user, Incident $incident): bool
    {
        return $this->isManagementUser($user);
    }

    protected function isManagementUser(User $user): bool
    {
        return in_array($user->role->value, UserRole::managementValues(), true);
    }
}
