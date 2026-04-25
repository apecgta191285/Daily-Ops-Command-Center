<?php

use App\Domain\Access\Enums\UserRole;
use App\Livewire\Admin\Users\Manage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = $this->createUserForRole(UserRole::Admin, [
        'name' => 'Admin Control',
        'email' => 'admin@example.com',
    ]);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor, [
        'name' => 'Supervisor Flow',
        'email' => 'supervisor@example.com',
    ]);
    $this->staff = $this->createUserForRole(UserRole::Staff, [
        'name' => 'Staff Operator',
        'email' => 'staff@example.com',
    ]);
});

test('admin can access user roster inside the main application shell', function () {
    $inactive = $this->createUserForRole(UserRole::Staff, [
        'name' => 'Dormant Operator',
        'email' => 'inactive@example.com',
        'is_active' => false,
    ]);

    $response = $this->actingAs($this->admin)->get(route('users.index'));

    $response->assertOk();
    $response->assertSee('บัญชีผู้ใช้งานในระบบ');
    $response->assertSee('สร้างผู้ใช้งาน');
    $response->assertSee('ความครอบคลุมของแต่ละบทบาท');
    $response->assertSee('Dormant Operator');
    $response->assertSee('ปิดการใช้งาน');
    $response->assertSee('ผู้ใช้งาน');
});

test('create user page shows lifecycle and role governance context', function () {
    $response = $this->actingAs($this->admin)->get(route('users.create'));

    $response->assertOk();
    $response->assertSee('สร้างบัญชีผู้ใช้');
    $response->assertSee('กำหนดหรือแก้สิทธิ์ใน 3 ขั้นตอน');
    $response->assertSee('ตำแหน่งของบัญชีนี้ในระบบ');
    $response->assertSee('ไม่มีขั้นตอนส่งอีเมลเชิญในหน้านี้');
});

test('edit user page shows existing lifecycle state and self-edit note when applicable', function () {
    $response = $this->actingAs($this->admin)->get(route('users.edit', $this->admin));

    $response->assertOk();
    $response->assertSee('แก้ไขบัญชีผู้ใช้');
    $response->assertSee('คุณกำลังแก้ไขบัญชีของตนเอง');
    $response->assertSee('ไม่สามารถเปลี่ยนบทบาทผู้ดูแลระบบของบัญชีตัวเองจากหน้านี้ได้');
    $response->assertSee('ไม่สามารถปิดการใช้งานสิทธิ์ผู้ดูแลระบบของบัญชีตัวเองจากหน้านี้ได้');
    $response->assertSee('บันทึกการเปลี่ยนแปลงบัญชี');
});

test('non admin users cannot access user administration routes', function () {
    $this->actingAs($this->supervisor)->get(route('users.index'))->assertForbidden();
    $this->actingAs($this->staff)->get(route('users.index'))->assertForbidden();
    $this->actingAs($this->supervisor)->get(route('users.create'))->assertForbidden();
    $this->actingAs($this->staff)->get(route('users.edit', $this->admin))->assertForbidden();
});

test('admin can create a managed user through the livewire administration surface', function () {
    Livewire::actingAs($this->admin)
        ->test(Manage::class)
        ->set('name', 'Ops New Hire')
        ->set('email', 'new.hire@example.com')
        ->set('role', UserRole::Staff->value)
        ->set('is_active', true)
        ->set('password', 'new-strong-password')
        ->set('password_confirmation', 'new-strong-password')
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'new.hire@example.com')->firstOrFail();

    expect($user->name)->toBe('Ops New Hire')
        ->and($user->role)->toBe(UserRole::Staff)
        ->and($user->is_active)->toBeTrue();
});

test('admin can update a managed user through the livewire administration surface', function () {
    Livewire::actingAs($this->admin)
        ->test(Manage::class, ['user' => $this->staff])
        ->set('name', 'Ops Shift Lead')
        ->set('email', 'shift.lead@example.com')
        ->set('role', UserRole::Supervisor->value)
        ->set('is_active', false)
        ->set('password', 'shift-lead-password')
        ->set('password_confirmation', 'shift-lead-password')
        ->call('save')
        ->assertHasNoErrors();

    $this->staff->refresh();

    expect($this->staff->name)->toBe('Ops Shift Lead')
        ->and($this->staff->email)->toBe('shift.lead@example.com')
        ->and($this->staff->role)->toBe(UserRole::Supervisor)
        ->and($this->staff->is_active)->toBeFalse();
});

test('admin cannot deactivate or demote their own account through the livewire administration surface', function () {
    Livewire::actingAs($this->admin)
        ->test(Manage::class, ['user' => $this->admin])
        ->set('role', UserRole::Supervisor->value)
        ->set('is_active', false)
        ->call('save')
        ->assertHasErrors(['role', 'is_active']);

    $this->admin->refresh();

    expect($this->admin->role)->toBe(UserRole::Admin)
        ->and($this->admin->is_active)->toBeTrue();
});
