<?php

use App\Domain\Access\Enums\UserRole;
use App\Livewire\Management\Reports\IncidentReport;
use App\Models\Incident;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-05-13 10:00:00'));

    $this->admin = $this->createUserForRole(UserRole::Admin);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->staff = $this->createUserForRole(UserRole::Staff);
    $this->room = $this->createRoom(['name' => 'Report Surface Lab', 'code' => 'RPT-S']);

    Incident::factory()->create([
        'title' => 'Report surface internet issue',
        'category' => 'เครือข่าย',
        'subcategory' => 'อินเทอร์เน็ต',
        'severity' => 'High',
        'status' => 'Open',
        'room_id' => $this->room->id,
        'created_by' => $this->staff->id,
        'created_at' => '2026-05-12 09:00:00',
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

test('management users can access incident report and staff cannot', function () {
    $this->actingAs($this->admin)->get(route('reports.incidents'))->assertOk();
    $this->actingAs($this->supervisor)->get(route('reports.incidents'))->assertOk();
    $this->actingAs($this->staff)->get(route('reports.incidents'))->assertForbidden();
});

test('incident report page renders filters aggregates and recent incidents', function () {
    $response = $this->actingAs($this->admin)->get(route('reports.incidents'));

    $response->assertOk();
    $response->assertSee('Incident Report');
    $response->assertSee('เลือกช่วงเวลาและมุมมองข้อมูล');
    $response->assertSee('ต้นตอปัญหาที่เกิดบ่อย');
    $response->assertSee('Report surface internet issue');
    $response->assertSee('อินเทอร์เน็ต');
});

test('incident report livewire filters by room category and subcategory', function () {
    Livewire::actingAs($this->admin)
        ->test(IncidentReport::class)
        ->set('roomId', (string) $this->room->id)
        ->set('category', 'เครือข่าย')
        ->set('subcategory', 'อินเทอร์เน็ต')
        ->assertSee('Report surface internet issue')
        ->assertSee('อินเทอร์เน็ต')
        ->set('subcategory', 'เครื่องพิมพ์')
        ->assertSet('subcategory', '');
});
