<?php

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Contracts\LoginResponse;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = $this->createUserForRole(UserRole::Admin);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->staff = $this->createUserForRole(UserRole::Staff);
    $this->createTemplateWithItems([
        'title' => 'Navigation template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);
});

test('post-login redirect lands on role-appropriate starting page', function () {
    $adminResponse = $this->actingAs($this->admin)->app->make(LoginResponse::class)->toResponse(request());
    expect($adminResponse->getTargetUrl())->toContain(route('dashboard', absolute: false));

    auth()->logout();

    $supervisorResponse = $this->actingAs($this->supervisor)->app->make(LoginResponse::class)->toResponse(request());
    expect($supervisorResponse->getTargetUrl())->toContain(route('dashboard', absolute: false));

    auth()->logout();

    $staffResponse = $this->actingAs($this->staff)->app->make(LoginResponse::class)->toResponse(request());
    expect($staffResponse->getTargetUrl())->toContain(route('checklists.runs.today', absolute: false));
});

test('admin sees management navigation links for dashboard incidents incident history run history checklist templates and users', function () {
    $response = $this->actingAs($this->admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('แดชบอร์ดภาพรวม');
    $response->assertSee('คิวปัญหา');
    $response->assertSee('ประวัติรายงานปัญหา');
    $response->assertSee('ประวัติรอบการตรวจเช็ก');
    $response->assertSee('แม่แบบรายการตรวจ');
    $response->assertSee('ผู้ใช้งาน');
    $response->assertDontSee(route('checklists.runs.today'), false);
    $response->assertDontSee(route('incidents.create'), false);
});

test('supervisor sees dashboard incidents incident history and run history navigation but not templates', function () {
    $response = $this->actingAs($this->supervisor)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('แดชบอร์ดภาพรวม');
    $response->assertSee('คิวปัญหา');
    $response->assertSee('ประวัติรายงานปัญหา');
    $response->assertSee('ประวัติรอบการตรวจเช็ก');
    $response->assertDontSee('แม่แบบรายการตรวจ');
    $response->assertDontSee('ผู้ใช้งาน');
    $response->assertDontSee(route('checklists.runs.today'), false);
});

test('staff sees checklist and incident creation navigation instead of forbidden dashboard links', function () {
    $response = $this->actingAs($this->staff)->get(route('checklists.runs.today'));

    $response->assertOk();
    $response->assertSee('รายการตรวจเช็กวันนี้');
    $response->assertSee('แจ้งรายงานปัญหา');
    $response->assertDontSee('แดชบอร์ดภาพรวม');
    $response->assertDontSee('ประวัติรายงานปัญหา');
    $response->assertDontSee('ประวัติรอบการตรวจเช็ก');
    $response->assertDontSee('แม่แบบรายการตรวจ');
    $response->assertDontSee('ผู้ใช้งาน');
});
