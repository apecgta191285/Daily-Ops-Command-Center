<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Contracts\LoginResponse;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->admin = User::where('role', 'admin')->first();
    $this->supervisor = User::where('role', 'supervisor')->first();
    $this->staff = User::where('role', 'staff')->first();
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

test('admin sees management navigation links for dashboard incidents and admin templates', function () {
    $response = $this->actingAs($this->admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
    $response->assertSee('Incidents');
    $response->assertSee('Admin Templates');
    $response->assertDontSee('Checklist Today');
    $response->assertDontSee('Report Incident');
});

test('supervisor sees dashboard and incidents navigation but not templates', function () {
    $response = $this->actingAs($this->supervisor)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
    $response->assertSee('Incidents');
    $response->assertDontSee('Admin Templates');
    $response->assertDontSee('Checklist Today');
});

test('staff sees checklist and incident creation navigation instead of forbidden dashboard links', function () {
    $response = $this->actingAs($this->staff)->get(route('checklists.runs.today'));

    $response->assertOk();
    $response->assertSee('Checklist Today');
    $response->assertSee('Report Incident');
    $response->assertDontSee('Dashboard');
    $response->assertDontSee('Admin Templates');
});
