<?php

use App\Domain\Access\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->activeStaff = $this->createUserForRole(UserRole::Staff, [
        'password' => 'password',
    ]);

    $this->inactiveUser = User::factory()
        ->staff()
        ->inactive()
        ->create([
            'name' => 'Inactive Operator',
            'email' => 'inactive@example.com',
            'password' => 'password',
        ]);
});

test('public registration route is unavailable', function () {
    $this->get('/register')->assertNotFound();
});

test('inactive users cannot authenticate', function () {
    $response = $this->from('/login')->post('/login', [
        'email' => $this->inactiveUser->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('inactive users are logged out when accessing protected routes', function () {
    $response = $this->actingAs($this->inactiveUser)->get(route('checklists.runs.today'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('active users can still authenticate through the standard login flow', function () {
    $response = $this->post('/login', [
        'email' => $this->activeStaff->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('checklists.runs.today', absolute: false));
    $this->assertAuthenticatedAs($this->activeStaff);
});
