<?php

declare(strict_types=1);

namespace App\Application\Users\Support;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Domain\Access\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserAdministrationValidator
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{name: string, email: string, role: string, is_active: bool, password: string}
     */
    public function validateCreate(array $attributes): array
    {
        /** @var array{name: string, email: string, role: string, is_active: bool, password: string} $validated */
        $validated = Validator::make($attributes, [
            'name' => $this->nameRules(),
            'email' => $this->emailRules(),
            'role' => ['required', 'string', Rule::in(UserRole::values())],
            'is_active' => ['required', 'boolean'],
            'password' => $this->passwordRules(),
        ])->validate();

        return [
            'name' => trim($validated['name']),
            'email' => Str::lower(trim($validated['email'])),
            'role' => $validated['role'],
            'is_active' => (bool) $validated['is_active'],
            'password' => $validated['password'],
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{name: string, email: string, role: string, is_active: bool, password?: string}
     */
    public function validateUpdate(User $user, array $attributes): array
    {
        $rules = [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($user->getKey()),
            'role' => ['required', 'string', Rule::in(UserRole::values())],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', Password::default(), 'confirmed'],
        ];

        /** @var array{name: string, email: string, role: string, is_active: bool, password?: string|null} $validated */
        $validated = Validator::make($attributes, $rules)->validate();

        $normalized = [
            'name' => trim($validated['name']),
            'email' => Str::lower(trim($validated['email'])),
            'role' => $validated['role'],
            'is_active' => (bool) $validated['is_active'],
        ];

        if (filled($validated['password'] ?? null)) {
            $normalized['password'] = $validated['password'];
        }

        return $normalized;
    }
}
