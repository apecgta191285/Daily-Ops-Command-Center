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
    $home->assertSee('ระบบงานประจำวันห้องปฏิบัติการคอมพิวเตอร์');
    $home->assertSee('ตรวจห้อง แจ้งปัญหา และติดตามงานของทีมจากพื้นที่ทำงานร่วมกันเพียงจุดเดียว');
    $home->assertSee('ใช้งานภายในสำหรับผู้ตรวจห้อง ผู้ดูแลห้องแล็บ และผู้ดูแลระบบ');

    $login->assertOk();
    $login->assertSee('ใช้บัญชีที่ได้รับมอบหมายเพื่อเข้าสู่งานประจำวันของทีมดูแลห้องคอม');
    $login->assertSee('ผู้ตรวจห้อง A');
    $login->assertSee('นักศึกษาที่เข้าเวรตรวจห้องและแจ้งรายงานปัญหาของห้อง');
    $login->assertSee('อาจารย์ผู้รับผิดชอบหรือผู้ได้รับมอบหมายที่ดูแลแม่แบบ ผู้ใช้งาน แดชบอร์ดภาพรวม และการติดตามงาน');
});

test('staff checklist surface uses lab-team wording for live checklist work', function () {
    $staff = $this->createUserForRole(UserRole::Staff);
    $room = $this->createRoom(['name' => 'Lab 1', 'code' => 'LAB-01']);
    $this->createTemplateWithItems([
        'title' => 'Lab opening check',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $response = $this->actingAs($staff)->get(route('checklists.runs.today', [
        'scope' => ChecklistScope::OPENING->routeKey(),
        'room' => $room->id,
    ]));

    $response->assertOk();
    $response->assertSee('รายการตรวจเช็กของผู้ตรวจห้อง');
    $response->assertSee('ตรวจเช็กห้องให้ครบ บันทึกสิ่งที่เกิดขึ้นจริง และส่งต่อปัญหาของห้องโดยไม่ทำให้บริบทของรายการตรวจเช็กหายไป');
    $response->assertSee('รายการตรวจเช็กประจำวัน');
});

test('admin governance surfaces use lab-team framing and expose the UI contract guide', function () {
    $admin = $this->createUserForRole(UserRole::Admin);

    $templates = $this->actingAs($admin)->get(route('templates.index'));
    $users = $this->actingAs($admin)->get(route('users.index'));
    $guide = $this->actingAs($admin)->get(route('ui-governance'));

    $templates->assertOk();
    $templates->assertSee('จัดการแม่แบบรายการตรวจที่ผู้ตรวจห้องใช้จริง');
    $templates->assertSee('แม่แบบที่ใช้งานจริงในแต่ละรอบตรวจ');

    $users->assertOk();
    $users->assertSee('จัดการบัญชีของอาจารย์ผู้รับผิดชอบ เจ้าหน้าที่แล็บ และผู้ตรวจห้อง');
    $users->assertSee('จัดการสิทธิ์ของทีมแล็บจากในระบบ');

    $guide->assertOk();
    $guide->assertSee('คู่มือคุมสัญญาหน้าจอ');
    $guide->assertSee('งานประจำวันของห้องปฏิบัติการคอมพิวเตอร์ในมหาวิทยาลัย');
    $guide->assertSee('ใช้ชุดไอคอนเดียวเท่านั้น');
});

test('major authenticated surfaces avoid leftover theatrical wording', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $staff = $this->createUserForRole(UserRole::Staff);
    $room = $this->createRoom(['name' => 'Lab 1', 'code' => 'LAB-01']);

    $this->createTemplateWithItems([
        'title' => 'Lab opening check',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $dashboard = $this->actingAs($admin)->get(route('dashboard'));
    $usersManage = $this->actingAs($admin)->get(route('users.create'));
    $templateManage = $this->actingAs($admin)->get(route('templates.create'));
    $checklist = $this->actingAs($staff)->get(route('checklists.runs.today', [
        'scope' => ChecklistScope::OPENING->routeKey(),
        'room' => $room->id,
    ]));

    $dashboard->assertOk();
    $dashboard->assertSee('งานปฏิบัติการแบบยึดห้องเป็นศูนย์กลาง');
    $dashboard->assertDontSee('Management visibility');

    $usersManage->assertOk();
    $usersManage->assertSee('การตั้งค่าบัญชีผู้ใช้');
    $usersManage->assertDontSee('database rituals');

    $templateManage->assertOk();
    $templateManage->assertSee('การจัดทำแม่แบบรายการตรวจ');
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
