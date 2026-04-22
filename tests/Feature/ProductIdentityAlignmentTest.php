<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest entry surfaces describe the product as a university computer lab operations system', function () {
    $home = $this->get(route('home'));
    $login = $this->get(route('login'));

    $home->assertOk();
    $home->assertSee('University Computer Lab Daily Ops');
    $home->assertSee('Run lab opening checks, catch room issues early, and keep the team aligned from one shared workspace.');
    $home->assertSee('Internal access for duty staff, lab supervisors, and admins');

    $login->assertOk();
    $login->assertSee('Use your assigned lab team account to continue into today’s lab work');
    $login->assertSee('Duty staff A');
    $login->assertSee('student on duty checking assigned rooms and reporting room issues');
    $login->assertSee('responsible lecturer or authorized academic owner');
});

test('staff checklist surface uses lab-team wording for live checklist work', function () {
    $staff = $this->createUserForRole(UserRole::Staff);
    $this->createTemplateWithItems([
        'title' => 'Lab opening check',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $response = $this->actingAs($staff)->get(route('checklists.runs.today'));

    $response->assertOk();
    $response->assertSee('Duty staff checklist');
    $response->assertSee('Complete the room check, record what actually happened there, and hand off room issues without losing the checklist context.');
    $response->assertSee('Daily Checklist');
});

test('admin governance surfaces use lab-team framing and expose the UI contract guide', function () {
    $admin = $this->createUserForRole(UserRole::Admin);

    $templates = $this->actingAs($admin)->get(route('templates.index'));
    $users = $this->actingAs($admin)->get(route('users.index'));
    $guide = $this->actingAs($admin)->get(route('ui-governance'));

    $templates->assertOk();
    $templates->assertSee('Govern the shared checklist lanes that students use when checking rooms');
    $templates->assertSee('Live checklist ownership by scope');

    $users->assertOk();
    $users->assertSee('Govern the lecturer, lab staff, and student duty accounts');
    $users->assertSee('Manage lab team access from inside the product');

    $guide->assertOk();
    $guide->assertSee('UI Contract Guide');
    $guide->assertSee('University Computer Lab Daily Ops');
    $guide->assertSee('One icon family only');
});

test('major authenticated surfaces avoid leftover theatrical wording', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $staff = $this->createUserForRole(UserRole::Staff);

    $this->createTemplateWithItems([
        'title' => 'Lab opening check',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $dashboard = $this->actingAs($admin)->get(route('dashboard'));
    $usersManage = $this->actingAs($admin)->get(route('users.create'));
    $templateManage = $this->actingAs($admin)->get(route('templates.create'));
    $checklist = $this->actingAs($staff)->get(route('checklists.runs.today'));

    $dashboard->assertOk();
    $dashboard->assertSee('Room-centered lab operations');
    $dashboard->assertDontSee('Management visibility');

    $usersManage->assertOk();
    $usersManage->assertSee('Account setup');
    $usersManage->assertDontSee('database rituals');

    $templateManage->assertOk();
    $templateManage->assertSee('Checklist drafting');
    $templateManage->assertDontSee('Authoring pulse');

    $checklist->assertOk();
    $checklist->assertDontSee('generic flow');
    $checklist->assertDontSee('evidence quality tight');
});

test('non admins cannot access the UI contract guide', function () {
    $supervisor = $this->createUserForRole(UserRole::Supervisor);
    $staff = $this->createUserForRole(UserRole::Staff);

    $this->actingAs($supervisor)->get(route('ui-governance'))->assertForbidden();
    $this->actingAs($staff)->get(route('ui-governance'))->assertForbidden();
});
