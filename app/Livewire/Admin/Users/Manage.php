<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Application\Users\Actions\CreateManagedUser;
use App\Application\Users\Actions\UpdateManagedUser;
use App\Application\Users\Support\UserRosterSummaryBuilder;
use App\Domain\Access\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Manage extends Component
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $role = '';

    public bool $is_active = true;

    public string $password = '';

    public string $password_confirmation = '';

    /** @var list<string> */
    public array $roles = [];

    public function mount(?User $user = null): void
    {
        $this->roles = UserRole::values();
        $this->user = $user;

        if (! $user) {
            $this->role = UserRole::Staff->value;

            return;
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->is_active = $user->is_active;
    }

    public function save(CreateManagedUser $createManagedUser, UpdateManagedUser $updateManagedUser): void
    {
        $this->resetErrorBag();

        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ];

        try {
            $managedUser = $this->user
                ? $updateManagedUser($this->user, $payload, auth()->user())
                : $createManagedUser($payload, auth()->user());
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());

            return;
        }

        session()->flash('message', $this->user
            ? 'User account updated successfully.'
            : 'User account created successfully.');

        $this->redirectRoute('users.edit', $managedUser, navigate: true);
    }

    public function getPageTitleProperty(): string
    {
        return $this->user ? 'Edit User Account' : 'Create User Account';
    }

    public function getPageDescriptionProperty(): string
    {
        return $this->user
            ? 'Update role, access state, and password control without leaving the main administration shell.'
            : 'Provision a new internal account with an explicit role, active state, and initial password.';
    }

    /**
     * @return list<array{
     *     tone: 'info'|'warning'|'success',
     *     title: string,
     *     body: string
     * }>
     */
    public function getLifecycleSignalsProperty(): array
    {
        $signals = [];

        if ($this->role === UserRole::Admin->value) {
            $signals[] = [
                'tone' => 'warning',
                'title' => 'Admin role is a governance lane',
                'body' => 'Administrators can manage templates, users, and other protected control surfaces. Use this role intentionally.',
            ];
        }

        if (! $this->is_active) {
            $signals[] = [
                'tone' => 'warning',
                'title' => 'Inactive accounts cannot sign in',
                'body' => 'Turning access off is immediate at authentication time. Keep inactive state for intentionally disabled accounts, not temporary confusion.',
            ];
        }

        if ($this->user && auth()->id() === $this->user->id) {
            $signals[] = [
                'tone' => 'info',
                'title' => 'You are editing your own account',
                'body' => 'Double-check role and active state carefully. WF3-B exposes lifecycle honestly, but later guard rails will harden self-management scenarios further.',
            ];
        }

        if ($this->user && filled($this->password)) {
            $signals[] = [
                'tone' => 'info',
                'title' => 'Saving will replace the current password',
                'body' => 'Leave password fields blank when you only want to update role, email, or active state.',
            ];
        }

        if (! $this->user) {
            $signals[] = [
                'tone' => 'success',
                'title' => 'Provisioning stays internal and explicit',
                'body' => 'WF3 keeps account creation inside admin control. No invitation flow or public registration is introduced here.',
            ];
        }

        return $signals === []
            ? [[
                'tone' => 'success',
                'title' => 'Lifecycle baseline looks healthy',
                'body' => 'This account can be updated safely from inside the product shell without falling back to manual database edits.',
            ]]
            : $signals;
    }

    /**
     * @return list<array{
     *     role: string,
     *     title: string,
     *     description: string,
     *     total_count: int,
     *     active_count: int,
     *     inactive_count: int,
     *     state: 'covered'|'warning',
     *     is_selected_role: bool
     * }>
     */
    public function getRoleGovernanceProperty(): array
    {
        return collect(app(UserRosterSummaryBuilder::class)()['role_lanes'])
            ->map(function (array $lane): array {
                $lane['is_selected_role'] = $lane['role'] === $this->role;

                return $lane;
            })
            ->all();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.users.manage', [
            'rosterSummary' => app(UserRosterSummaryBuilder::class)(),
        ]);
    }
}
