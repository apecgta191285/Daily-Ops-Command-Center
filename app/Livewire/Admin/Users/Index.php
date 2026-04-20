<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Application\Users\Support\UserRosterSummaryBuilder;
use App\Domain\Access\Enums\UserRole;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.users.index', [
            'users' => User::query()
                ->withCount('ownedIncidents')
                ->orderByRaw('case role when ? then 0 when ? then 1 else 2 end', [UserRole::Admin->value, UserRole::Supervisor->value])
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->get(),
            'rosterSummary' => app(UserRosterSummaryBuilder::class)(),
        ]);
    }
}
