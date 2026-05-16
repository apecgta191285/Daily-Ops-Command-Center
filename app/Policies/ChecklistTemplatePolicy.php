<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ChecklistTemplate;
use App\Models\User;

class ChecklistTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ChecklistTemplate $template): bool
    {
        return $user->isAdmin();
    }

    public function duplicate(User $user, ChecklistTemplate $template): bool
    {
        return $user->isAdmin();
    }
}
