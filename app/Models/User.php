<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domain\Access\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // ── Role Helpers ──────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin->value;
    }

    public function isSupervisor(): bool
    {
        return $this->role === UserRole::Supervisor->value;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::Staff->value;
    }

    public function isManagement(): bool
    {
        return in_array($this->role, UserRole::managementValues(), true);
    }

    public function landingRouteName(): string
    {
        return $this->isManagement() ? 'dashboard' : 'checklists.runs.today';
    }

    // ── Day 2A Relationships ──────────────────────────────

    public function checklistRuns(): HasMany
    {
        return $this->hasMany(ChecklistRun::class, 'created_by');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'created_by');
    }
}
