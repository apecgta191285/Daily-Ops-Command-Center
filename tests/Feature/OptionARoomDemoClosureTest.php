<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Livewire\Staff\Checklists\DailyRun;
use App\Livewire\Staff\Incidents\Create as CreateIncidentForm;
use App\Models\ChecklistRun;
use App\Models\Incident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = $this->createUserForRole(UserRole::Admin, ['name' => 'Lecturer Owner']);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor, ['name' => 'Room Caretaker']);
    $this->staff = $this->createUserForRole(UserRole::Staff, ['name' => 'Duty Student']);

    $this->roomA = $this->createRoom([
        'name' => 'Lab 1',
        'code' => 'LAB-01',
    ]);

    $this->roomB = $this->createRoom([
        'name' => 'Lab 2',
        'code' => 'LAB-02',
    ]);

    $this->openingTemplate = $this->createTemplateWithItems([
        'title' => 'Opening room checklist',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ], [
        ['title' => 'Unlock room', 'description' => 'Open the lab and confirm access', 'group_label' => 'Opening checks'],
        ['title' => 'Check workstation readiness', 'description' => 'Confirm the room is ready for students', 'group_label' => 'Opening checks'],
    ]);
});

test('option A demo flow keeps room context from checklist through management follow-up', function () {
    Livewire::actingAs($this->staff)
        ->test(DailyRun::class)
        ->assertSet('errorState', 'room_required')
        ->assertSee('เริ่มจากเลือกห้องที่จะตรวจวันนี้', escape: false)
        ->assertSee($this->roomA->name)
        ->assertSee($this->roomB->name);

    $dailyRun = Livewire::actingAs($this->staff)
        ->withQueryParams([
            'room' => (string) $this->roomA->id,
            'scope' => ChecklistScope::OPENING->routeKey(),
        ])
        ->test(DailyRun::class)
        ->assertSet('room', (string) $this->roomA->id)
        ->assertSee($this->roomA->name)
        ->assertSee('ส่งรายการตรวจเช็ก');

    $runItems = $dailyRun->get('runItems');
    $itemIds = array_keys($runItems);

    $dailyRun
        ->set("runItems.{$itemIds[0]}.result", ChecklistResult::Done->value)
        ->set("runItems.{$itemIds[1]}.result", ChecklistResult::NotDone->value)
        ->set("runItems.{$itemIds[1]}.note", 'PC-12 did not power on during opening check.')
        ->call('submit')
        ->assertHasNoErrors();

    $run = ChecklistRun::query()
        ->where('created_by', $this->staff->id)
        ->where('room_id', $this->roomA->id)
        ->where('checklist_template_id', $this->openingTemplate->id)
        ->firstOrFail();

    expect($run->submitted_at)->not->toBeNull();
    expect($run->room_id)->toBe($this->roomA->id);

    $incidentCreate = Livewire::actingAs($this->staff)
        ->withQueryParams([
            'title' => 'Lab 1 opening issue follow-up',
            'category' => 'อุปกรณ์คอมพิวเตอร์',
            'severity' => 'Medium',
            'description' => 'PC-12 did not power on during opening check.',
            'room' => (string) $this->roomA->id,
            'checklist_scope' => ChecklistScope::OPENING->routeKey(),
        ])
        ->test(CreateIncidentForm::class)
        ->assertSet('roomId', (string) $this->roomA->id);

    $incidentCreate
        ->set('title', 'Lab 1 workstation power issue')
        ->set('category', 'อุปกรณ์คอมพิวเตอร์')
        ->set('severity', 'High')
        ->set('equipmentReference', 'PC-12')
        ->set('description', 'PC-12 failed to power on during the opening room check.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSee('สรุปการส่งรายงาน')
        ->assertSee($this->roomA->name)
        ->assertSee('PC-12');

    $incident = Incident::query()
        ->where('title', 'Lab 1 workstation power issue')
        ->firstOrFail();

    expect($incident->room_id)->toBe($this->roomA->id);
    expect($incident->equipment_reference)->toBe('PC-12');

    $this->actingAs($this->supervisor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Lab 1 workstation power issue')
        ->assertSee($this->roomA->name)
        ->assertSee('PC-12');

    $this->actingAs($this->supervisor)
        ->get(route('incidents.index'))
        ->assertOk()
        ->assertSee('Lab 1 workstation power issue')
        ->assertSee($this->roomA->name)
        ->assertSee('PC-12');

    $this->actingAs($this->supervisor)
        ->get(route('incidents.show', $incident))
        ->assertOk()
        ->assertSee($this->roomA->name)
        ->assertSee('PC-12');

    $this->actingAs($this->supervisor)
        ->get(route('checklists.history.show', $run))
        ->assertOk()
        ->assertSee($this->roomA->name)
        ->assertSee('PC-12 did not power on during opening check.');
});

test('option A still keeps admin governance usable inside the room-centered case study', function () {
    $this->actingAs($this->admin)
        ->get(route('templates.index'))
        ->assertOk()
        ->assertSee('จัดการแม่แบบรายการตรวจที่ผู้ตรวจห้องใช้จริง')
        ->assertSee('ยึดห้องเป็นศูนย์กลาง');

    $this->actingAs($this->admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('อาจารย์ผู้รับผิดชอบ เจ้าหน้าที่แล็บ และผู้ตรวจห้อง')
        ->assertSee('workflow การตรวจห้อง');
});
