<?php

declare(strict_types=1);

namespace App\Application\Users\Actions;

use App\Application\Users\Support\UserAdministrationValidator;
use App\Application\Users\Support\UserLifecycleGuardRail;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class UpdateManagedUser
{
    public function __construct(
        private readonly UserAdministrationValidator $validator,
        private readonly UserLifecycleGuardRail $guardRail,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(User $user, array $attributes, User $actor): User
    {
        $this->ensureAdmin($actor);

        $validated = $this->validator->validateUpdate($user, $attributes);
        $this->guardRail->enforce($user, $validated, $actor);

        DB::transaction(function () use ($user, $validated): void {
            $payload = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'is_active' => $validated['is_active'],
            ];

            if (isset($validated['password'])) {
                $payload['password'] = $validated['password'];
            }

            $user->update($payload);
        });

        return $user->fresh();
    }

    private function ensureAdmin(User $actor): void
    {
        if (! $actor->isAdmin()) {
            throw new AuthorizationException('Only administrators can manage users.');
        }
    }
}
