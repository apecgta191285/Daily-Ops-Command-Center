<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can view the profile settings surface with the cleaned control layout', function () {
    $user = $this->createUserForRole(UserRole::Admin);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertOk();
    $response->assertSee('Settings');
    $response->assertSee('Account workspace');
    $response->assertSee('Profile details');
    $response->assertSee('What this account controls');
});

test('authenticated users can view the security settings surface with password and two-factor sections', function () {
    $user = $this->createUserForRole(UserRole::Admin);
    $user->forceFill([
        'email_verified_at' => now(),
    ])->save();

    $response = $this
        ->withSession(['auth.password_confirmed_at' => time()])
        ->actingAs($user)
        ->get(route('security.edit'));

    $response->assertOk();
    $response->assertSee('Password hygiene');
    $response->assertSee('Two-factor authentication');
    $response->assertSee('Verification layer');
});
