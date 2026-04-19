<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Application\Users\Support\UserRosterSummaryBuilder;
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
                ->orderByRaw("case role when 'admin' then 0 when 'supervisor' then 1 else 2 end")
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->get(),
            'rosterSummary' => app(UserRosterSummaryBuilder::class)(),
        ]);
    }
}
