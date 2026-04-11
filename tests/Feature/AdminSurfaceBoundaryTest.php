<?php

use App\Filament\Resources\ChecklistTemplates\ChecklistTemplateResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->admin = User::where('role', 'admin')->firstOrFail();
    $this->supervisor = User::where('role', 'supervisor')->firstOrFail();
});

test('admin can access the filament panel but supervisor cannot', function () {
    $panel = filament()->getPanel('admin');

    expect($this->admin->canAccessPanel($panel))->toBeTrue();
    expect($this->supervisor->canAccessPanel($panel))->toBeFalse();
});

test('templates route is an explicit bridge page instead of a direct admin redirect', function () {
    $response = $this->actingAs($this->admin)->get(route('templates.index'));

    $response->assertOk();
    $response->assertSee('Admin surface ahead.');
    $response->assertSee('Open admin template management');
    $response->assertSee(ChecklistTemplateResource::getUrl('index', panel: 'admin'), escape: false);
});

test('supervisor cannot access the admin panel root', function () {
    $response = $this->actingAs($this->supervisor)->get('/admin');

    $response->assertForbidden();
});

test('admin panel root redirects admin to template management', function () {
    $response = $this->actingAs($this->admin)->get('/admin');

    $response->assertRedirect(ChecklistTemplateResource::getUrl('index', panel: 'admin'));
});
