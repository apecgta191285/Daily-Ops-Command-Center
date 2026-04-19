<?php

declare(strict_types=1);

namespace App\Application\Users\Actions;

use App\Application\Users\Support\UserAdministrationValidator;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class CreateManagedUser
{
    public function __construct(
        private readonly UserAdministrationValidator $validator,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(array $attributes, User $actor): User
    {
        $this->ensureAdmin($actor);

        $validated = $this->validator->validateCreate($attributes);

        return User::query()->create($validated);
    }

    private function ensureAdmin(User $actor): void
    {
        if (! $actor->isAdmin()) {
            throw new AuthorizationException('Only administrators can manage users.');
        }
    }
}
