<?php

use App\Domain\Access\Enums\UserRole;
use App\Livewire\Staff\Incidents\Create;
use App\Models\Incident;
use App\Models\IncidentActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->admin = User::where('role', UserRole::Admin->value)->first();
    $this->supervisor = User::where('role', UserRole::Supervisor->value)->first();
    $this->operatorA = User::where('email', 'operatora@example.com')->first();
});

test('route access audit for incidents/new', function () {
    // Unauthenticated -> redirect to login
    $this->get('/incidents/new')->assertRedirect('/login');

    // Admin -> 403 Forbidden
    $this->actingAs($this->admin)->get('/incidents/new')->assertForbidden();

    // Supervisor -> 403 Forbidden
    $this->actingAs($this->supervisor)->get('/incidents/new')->assertForbidden();

    // Staff -> 200 OK
    $this->actingAs($this->operatorA)->get('/incidents/new')->assertOk();
});

test('incident creation with all required fields persists correctly', function () {
    $incidentCountBefore = Incident::count();
    $activityCountBefore = IncidentActivity::count();

    Livewire::actingAs($this->operatorA)
        ->test(Create::class)
        ->set('title', 'Test PC broken')
        ->set('category', 'อุปกรณ์คอมพิวเตอร์')
        ->set('severity', 'Medium')
        ->set('description', 'PC-99 will not boot at all.')
        ->call('submit')
        ->assertHasNoErrors();

    // Verify incident was created
    expect(Incident::count())->toBe($incidentCountBefore + 1);

    $incident = Incident::latest('id')->first();
    expect($incident->title)->toBe('Test PC broken');
    expect($incident->category)->toBe('อุปกรณ์คอมพิวเตอร์');
    expect($incident->severity)->toBe('Medium');
    expect($incident->status)->toBe('Open');
    expect($incident->description)->toBe('PC-99 will not boot at all.');
    expect($incident->attachment_path)->toBeNull();
    expect($incident->created_by)->toBe($this->operatorA->id);
    expect($incident->resolved_at)->toBeNull();

    // Verify activity was created
    expect(IncidentActivity::count())->toBe($activityCountBefore + 1);

    $activity = $incident->activities()->first();
    expect($activity->action_type)->toBe('created');
    expect($activity->summary)->toBe('Incident reported');
    expect($activity->actor_id)->toBe($this->operatorA->id);
});

test('incident creation validation blocks missing required fields', function () {
    Livewire::actingAs($this->operatorA)
        ->test(Create::class)
        ->call('submit')
        ->assertHasErrors(['title', 'category', 'severity', 'description']);
});

test('incident creation validation blocks invalid category', function () {
    Livewire::actingAs($this->operatorA)
        ->test(Create::class)
        ->set('title', 'Test')
        ->set('category', 'InvalidCategory')
        ->set('severity', 'Low')
        ->set('description', 'desc')
        ->call('submit')
        ->assertHasErrors(['category']);
});

test('incident creation validation blocks invalid severity', function () {
    Livewire::actingAs($this->operatorA)
        ->test(Create::class)
        ->set('title', 'Test')
        ->set('category', 'เครือข่าย')
        ->set('severity', 'Critical')
        ->set('description', 'desc')
        ->call('submit')
        ->assertHasErrors(['severity']);
});

test('incident creation with optional attachment persists file path', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('evidence.pdf', 100, 'application/pdf');

    Livewire::actingAs($this->operatorA)
        ->test(Create::class)
        ->set('title', 'Broken monitor with photo')
        ->set('category', 'อุปกรณ์คอมพิวเตอร์')
        ->set('severity', 'High')
        ->set('description', 'Monitor cracked.')
        ->set('attachment', $file)
        ->call('submit')
        ->assertHasNoErrors();

    $incident = Incident::latest('id')->first();
    expect($incident->attachment_path)->not->toBeNull();
    expect($incident->attachment_path)->toContain('incidents/');

    Storage::disk('public')->assertExists($incident->attachment_path);
});

test('incident creation without attachment still succeeds', function () {
    Livewire::actingAs($this->operatorA)
        ->test(Create::class)
        ->set('title', 'No photo incident')
        ->set('category', 'ความสะอาด')
        ->set('severity', 'Low')
        ->set('description', 'Minor dust.')
        ->call('submit')
        ->assertHasNoErrors();

    $incident = Incident::latest('id')->first();
    expect($incident->attachment_path)->toBeNull();
    expect($incident->status)->toBe('Open');
});
