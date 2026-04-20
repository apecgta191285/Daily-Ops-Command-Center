<?php

use App\Domain\Access\Enums\UserRole;
use App\Livewire\Admin\Users\Manage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = $this->createUserForRole(UserRole::Admin, [
        'name' => 'Admin Control',
        'email' => 'admin@example.com',
    ]);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor, [
        'name' => 'Supervisor Flow',
        'email' => 'supervisor@example.com',
    ]);
    $this->staff = $this->createUserForRole(UserRole::Staff, [
        'name' => 'Staff Operator',
        'email' => 'staff@example.com',
    ]);
});

test('admin can access user roster inside the main application shell', function () {
    $inactive = $this->createUserForRole(UserRole::Staff, [
        'name' => 'Dormant Operator',
        'email' => 'inactive@example.com',
        'is_active' => false,
    ]);

    $response = $this->actingAs($this->admin)->get(route('users.index'));

    $response->assertOk();
    $response->assertSee('Team Access Roster');
    $response->assertSee('Create user');
    $response->assertSee('Coverage by role lane');
    $response->assertSee('Dormant Operator');
    $response->assertSee('Inactive');
    $response->assertSee('Users');
});

test('create user page shows lifecycle and role governance context', function () {
    $response = $this->actingAs($this->admin)->get(route('users.create'));

    $response->assertOk();
    $response->assertSee('Create User Account');
    $response->assertSee('Provision or revise access in three passes');
    $response->assertSee('Where this account will sit');
    $response->assertSee('No invitation email flow here');
});

test('edit user page shows existing lifecycle state and self-edit note when applicable', function () {
    $response = $this->actingAs($this->admin)->get(route('users.edit', $this->admin));

    $response->assertOk();
    $response->assertSee('Edit User Account');
    $response->assertSee('You are editing your own account');
    $response->assertSee('Your own administrator role cannot be changed from this screen.');
    $response->assertSee('Your own administrator access cannot be deactivated from this screen.');
    $response->assertSee('Save account changes');
});

test('non admin users cannot access user administration routes', function () {
    $this->actingAs($this->supervisor)->get(route('users.index'))->assertForbidden();
    $this->actingAs($this->staff)->get(route('users.index'))->assertForbidden();
    $this->actingAs($this->supervisor)->get(route('users.create'))->assertForbidden();
    $this->actingAs($this->staff)->get(route('users.edit', $this->admin))->assertForbidden();
});

test('admin can create a managed user through the livewire administration surface', function () {
    Livewire::actingAs($this->admin)
        ->test(Manage::class)
        ->set('name', 'Ops New Hire')
        ->set('email', 'new.hire@example.com')
        ->set('role', UserRole::Staff->value)
        ->set('is_active', true)
        ->set('password', 'new-strong-password')
        ->set('password_confirmation', 'new-strong-password')
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'new.hire@example.com')->firstOrFail();

    expect($user->name)->toBe('Ops New Hire')
        ->and($user->role)->toBe(UserRole::Staff->value)
        ->and($user->is_active)->toBeTrue();
});

test('admin can update a managed user through the livewire administration surface', function () {
    Livewire::actingAs($this->admin)
        ->test(Manage::class, ['user' => $this->staff])
        ->set('name', 'Ops Shift Lead')
        ->set('email', 'shift.lead@example.com')
        ->set('role', UserRole::Supervisor->value)
        ->set('is_active', false)
        ->set('password', 'shift-lead-password')
        ->set('password_confirmation', 'shift-lead-password')
        ->call('save')
        ->assertHasNoErrors();

    $this->staff->refresh();

    expect($this->staff->name)->toBe('Ops Shift Lead')
        ->and($this->staff->email)->toBe('shift.lead@example.com')
        ->and($this->staff->role)->toBe(UserRole::Supervisor->value)
        ->and($this->staff->is_active)->toBeFalse();
});

test('admin cannot deactivate or demote their own account through the livewire administration surface', function () {
    Livewire::actingAs($this->admin)
        ->test(Manage::class, ['user' => $this->admin])
        ->set('role', UserRole::Supervisor->value)
        ->set('is_active', false)
        ->call('save')
        ->assertHasErrors(['role', 'is_active']);

    $this->admin->refresh();

    expect($this->admin->role)->toBe(UserRole::Admin->value)
        ->and($this->admin->is_active)->toBeTrue();
});
