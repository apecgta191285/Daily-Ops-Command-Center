<?php

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest-facing home and login surfaces render without browser smoke issues', function () {
    [$homePage, $loginPage] = visit(['/', '/login']);

    $homePage
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Daily Ops Command Center')
        ->assertSee('Log in')
        ->assertSee('Suggested demo walkthrough');

    $loginPage
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Log in to your account')
        ->assertSee('Local demo accounts')
        ->assertPresent('input[name="email"]')
        ->assertPresent('[data-test="login-button"]');
});

test('admin can authenticate and reach checklist template administration in the main app shell', function () {
    $admin = $this->createUserForRole(UserRole::Admin);

    $this->createTemplateWithItems([
        'title' => 'Browser smoke template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $page = visit('/login');

    $page->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Dashboard')
        ->click('Checklist Templates')
        ->assertPathIs('/templates')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Checklist Templates')
        ->assertSee('Create template');
});

test('staff can authenticate into the daily checklist workflow without browser smoke issues', function () {
    $staff = $this->createUserForRole(UserRole::Staff);

    $this->createTemplateWithItems([
        'title' => 'Staff browser smoke template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $page = visit('/login');

    $page->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Daily Checklist')
        ->assertSee('Report Incident')
        ->assertSee('Submit Checklist');
});
