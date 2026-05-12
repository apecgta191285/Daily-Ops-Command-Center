<?php

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Livewire\Admin\Rooms\Manage as RoomManage;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = $this->createUserForRole(UserRole::Admin, [
        'name' => 'Room Admin',
        'email' => 'room.admin@example.com',
    ]);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->staff = $this->createUserForRole(UserRole::Staff);
});

test('admin can access room roster inside the main application shell', function () {
    $this->createRoom([
        'name' => 'Lab Protected',
        'code' => 'LAB-PROT',
    ]);
    $this->createRoom([
        'name' => 'Lab Dormant',
        'code' => 'LAB-OFF',
        'is_active' => false,
    ]);

    $response = $this->actingAs($this->admin)->get(route('rooms.index'));

    $response->assertOk();
    $response->assertSee('Room Master Data');
    $response->assertSee('ทำให้ทุก workflow อ้างอิงห้องชุดเดียวกัน');
    $response->assertSee('Lab Protected');
    $response->assertSee('LAB-OFF');
    $response->assertSee('ปิดการใช้งาน');
});

test('create room page explains audit-safe lifecycle', function () {
    $response = $this->actingAs($this->admin)->get(route('rooms.create'));

    $response->assertOk();
    $response->assertSee('สร้างห้อง');
    $response->assertSee('ชื่อ รหัส และคำอธิบาย');
    $response->assertSee('ระบบแยกการปิดใช้งานออกจากการลบ');
});

test('edit room page locks deletion when room has operational history', function () {
    $room = $this->createRoom(['name' => 'Lab Locked', 'code' => 'LAB-LOCK']);
    $template = $this->createTemplateWithItems([
        'title' => 'Room locked template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);
    $this->createRunForUser($this->staff, $template, submitted: true, room: $room);

    $response = $this->actingAs($this->admin)->get(route('rooms.edit', $room));

    $response->assertOk();
    $response->assertSee('ห้องนี้มีประวัติการใช้งานแล้ว');
    $response->assertSee('ปุ่มลบถูกปิดไว้เพราะห้องนี้มีข้อมูล operational history แล้ว');
});

test('non admin users cannot access room administration routes', function () {
    $room = $this->createRoom(['code' => 'LAB-AUTH']);

    $this->actingAs($this->supervisor)->get(route('rooms.index'))->assertForbidden();
    $this->actingAs($this->staff)->get(route('rooms.index'))->assertForbidden();
    $this->actingAs($this->supervisor)->get(route('rooms.create'))->assertForbidden();
    $this->actingAs($this->staff)->get(route('rooms.edit', $room))->assertForbidden();
});

test('admin can create and update a room through the livewire administration surface', function () {
    Livewire::actingAs($this->admin)
        ->test(RoomManage::class)
        ->set('name', 'Lab Analytics')
        ->set('code', 'lab-analytics')
        ->set('description', 'Analytics training room')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors();

    $room = Room::query()->where('code', 'LAB-ANALYTICS')->firstOrFail();

    expect($room->name)->toBe('Lab Analytics')
        ->and($room->is_active)->toBeTrue();

    Livewire::actingAs($this->admin)
        ->test(RoomManage::class, ['room' => $room])
        ->set('name', 'Lab Analytics Retired')
        ->set('is_active', false)
        ->call('save')
        ->assertHasNoErrors();

    $room->refresh();

    expect($room->name)->toBe('Lab Analytics Retired')
        ->and($room->is_active)->toBeFalse();
});

test('admin can delete only unused rooms through the livewire surface', function () {
    $unusedRoom = $this->createRoom(['code' => 'LAB-DELETE']);
    $protectedRoom = $this->createRoom(['code' => 'LAB-PROTECTED']);
    $template = $this->createTemplateWithItems([
        'title' => 'Room protected surface template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);
    $this->createRunForUser($this->staff, $template, submitted: true, room: $protectedRoom);

    Livewire::actingAs($this->admin)
        ->test(RoomManage::class, ['room' => $unusedRoom])
        ->call('delete')
        ->assertHasNoErrors();

    expect(Room::query()->whereKey($unusedRoom->id)->exists())->toBeFalse();

    Livewire::actingAs($this->admin)
        ->test(RoomManage::class, ['room' => $protectedRoom])
        ->call('delete')
        ->assertHasErrors(['room']);

    expect(Room::query()->whereKey($protectedRoom->id)->exists())->toBeTrue();
});
