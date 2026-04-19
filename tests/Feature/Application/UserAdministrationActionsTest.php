<?php

use App\Application\Users\Actions\CreateManagedUser;
use App\Application\Users\Actions\UpdateManagedUser;
use App\Domain\Access\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $this->supervisor = User::factory()->supervisor()->create([
        'name' => 'Supervisor User',
        'email' => 'supervisor@example.com',
        'password' => 'password',
    ]);

    $this->staff = User::factory()->staff()->create([
        'name' => 'Staff User',
        'email' => 'staff@example.com',
        'password' => 'password',
    ]);
});

test('admin can create an internal user with explicit initial password', function () {
    $user = app(CreateManagedUser::class)([
        'name' => 'New Operator',
        'email' => 'NEW.OPERATOR@example.com',
        'role' => UserRole::Staff->value,
        'is_active' => true,
        'password' => 'strong-password',
        'password_confirmation' => 'strong-password',
    ], $this->admin);

    expect($user->name)->toBe('New Operator')
        ->and($user->email)->toBe('new.operator@example.com')
        ->and($user->role)->toBe(UserRole::Staff->value)
        ->and($user->is_active)->toBeTrue()
        ->and(Hash::check('strong-password', $user->password))->toBeTrue();
});

test('create managed user rejects invalid role', function () {
    expect(fn () => app(CreateManagedUser::class)([
        'name' => 'Broken User',
        'email' => 'broken@example.com',
        'role' => 'guest',
        'is_active' => true,
        'password' => 'strong-password',
        'password_confirmation' => 'strong-password',
    ], $this->admin))->toThrow(ValidationException::class);
});

test('admin can update role active state and password for an existing user', function () {
    $updated = app(UpdateManagedUser::class)(
        $this->staff,
        [
            'name' => 'Operator Prime',
            'email' => 'operator.prime@example.com',
            'role' => UserRole::Supervisor->value,
            'is_active' => false,
            'password' => 'new-strong-password',
            'password_confirmation' => 'new-strong-password',
        ],
        $this->admin,
    );

    expect($updated->name)->toBe('Operator Prime')
        ->and($updated->email)->toBe('operator.prime@example.com')
        ->and($updated->role)->toBe(UserRole::Supervisor->value)
        ->and($updated->is_active)->toBeFalse()
        ->and(Hash::check('new-strong-password', $updated->password))->toBeTrue();
});

test('inactive user still cannot authenticate after admin deactivation', function () {
    app(UpdateManagedUser::class)(
        $this->staff,
        [
            'name' => $this->staff->name,
            'email' => $this->staff->email,
            'role' => $this->staff->role,
            'is_active' => false,
        ],
        $this->admin,
    );

    $response = $this->from('/login')->post('/login', [
        'email' => $this->staff->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('non admin users cannot create or update managed users', function () {
    expect(fn () => app(CreateManagedUser::class)([
        'name' => 'Blocked User',
        'email' => 'blocked@example.com',
        'role' => UserRole::Staff->value,
        'is_active' => true,
        'password' => 'strong-password',
        'password_confirmation' => 'strong-password',
    ], $this->supervisor))->toThrow(AuthorizationException::class);

    expect(fn () => app(UpdateManagedUser::class)(
        $this->staff,
        [
            'name' => $this->staff->name,
            'email' => $this->staff->email,
            'role' => $this->staff->role,
            'is_active' => true,
        ],
        $this->supervisor,
    ))->toThrow(AuthorizationException::class);
});

test('update managed user rejects duplicate email safely', function () {
    expect(fn () => app(UpdateManagedUser::class)(
        $this->staff,
        [
            'name' => $this->staff->name,
            'email' => $this->supervisor->email,
            'role' => $this->staff->role,
            'is_active' => true,
        ],
        $this->admin,
    ))->toThrow(ValidationException::class);
});

test('admin cannot deactivate their own administrator account', function () {
    expect(fn () => app(UpdateManagedUser::class)(
        $this->admin,
        [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'role' => UserRole::Admin->value,
            'is_active' => false,
        ],
        $this->admin,
    ))->toThrow(ValidationException::class);
});

test('admin cannot remove administrator role from their own account', function () {
    expect(fn () => app(UpdateManagedUser::class)(
        $this->admin,
        [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'role' => UserRole::Supervisor->value,
            'is_active' => true,
        ],
        $this->admin,
    ))->toThrow(ValidationException::class);
});

test('admin can deactivate another administrator when another active admin still remains', function () {
    $secondAdmin = User::factory()->admin()->create([
        'name' => 'Second Admin',
        'email' => 'second-admin@example.com',
        'password' => 'password',
    ]);

    $updated = app(UpdateManagedUser::class)(
        $secondAdmin,
        [
            'name' => $secondAdmin->name,
            'email' => $secondAdmin->email,
            'role' => UserRole::Admin->value,
            'is_active' => false,
        ],
        $this->admin,
    );

    expect($updated->is_active)->toBeFalse();
});
