<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function stabilizeVisualState($page)
{
    $page->script(<<<'JS'
        if (! document.getElementById('browser-qa-disable-motion')) {
            const style = document.createElement('style');
            style.id = 'browser-qa-disable-motion';
            style.textContent = `
                *,
                *::before,
                *::after {
                    animation: none !important;
                    transition: none !important;
                    caret-color: auto !important;
                }

                [data-motion],
                [data-motion] *,
                [data-motion] *::before,
                [data-motion] *::after {
                    opacity: 1 !important;
                    transform: none !important;
                    filter: none !important;
                }

                .auth-panel,
                .auth-panel *,
                .ops-card,
                .ops-card *,
                .ops-hero,
                .ops-hero *,
                .ops-workboard,
                .ops-workboard *,
                .ops-bucket-board,
                .ops-bucket-board *,
                .ops-context-board,
                .ops-context-board *,
                .ops-incident-panel,
                .ops-incident-panel *,
                .ops-incident-lane,
                .ops-incident-lane * {
                    opacity: 1 !important;
                    transform: none !important;
                    filter: none !important;
                }
            `;

            document.head.appendChild(style);
        }

        document.querySelectorAll('[data-motion]').forEach((element) => {
            element.classList.add('is-visible');
            element.style.transition = 'none';
            element.style.opacity = '1';
            element.style.transform = 'none';
            element.style.filter = 'none';
        });
        document.querySelectorAll('[data-meter-target]').forEach((meter) => {
            meter.style.transition = 'none';
            meter.style.width = `${meter.dataset.meterTarget ?? '0'}%`;
        });

        document.querySelectorAll('[autofocus]').forEach((element) => {
            element.removeAttribute('autofocus');
        });

        if (document.activeElement instanceof HTMLElement) {
            document.activeElement.blur();
        }

        window.scrollTo(0, 0);
    JS);

    return $page->wait(0.35);
}

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-04-20 09:00:00'));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('guest-facing home and login surfaces render without browser smoke issues', function () {
    [$homePage, $loginPage] = visit(['/', '/login']);

    $homePage
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertNoAccessibilityIssues()
        ->assertSee('Daily Ops Command Center')
        ->assertSee('ระบบงานประจำวันห้องปฏิบัติการคอมพิวเตอร์')
        ->assertSee('เข้าสู่ระบบ')
        ->assertSee('ลำดับเดโมที่แนะนำ')
        ->assertPresent('a[href="#main-content"]');

    $loginPage
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertNoAccessibilityIssues()
        ->assertSee('ใช้บัญชีที่ได้รับมอบหมายเพื่อเข้าสู่งานประจำวันของทีมดูแลห้องคอม')
        ->assertSee('บัญชีเดโมสำหรับเครื่อง local')
        ->assertPresent('input[name="email"]')
        ->assertPresent('a[href="#main-content"]')
        ->assertPresent('[data-test="login-button"]');
});

test('admin can authenticate and reach checklist template administration in the main app shell', function () {
    $admin = $this->createUserForRole(UserRole::Admin);

    $this->createTemplateWithItems([
        'title' => 'Browser smoke template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $page = visit('/login');

    $page->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertPresent('body > [data-flux-sidebar]')
        ->assertPresent('body > [data-flux-header]')
        ->assertPresent('body > [data-flux-main]')
        ->assertSee('แดชบอร์ดภาพรวม')
        ->assertSee('งานปฏิบัติการแบบยึดห้องเป็นศูนย์กลาง')
        ->assertSee('กระดานงานห้องแล็บของวันนี้')
        ->assertSee('ภาพรวมการทำงาน')
        ->assertSee('ตรวจคิวปัญหา')
        ->assertPresent('svg.ops-arc')
        ->assertPresent('svg.ops-sparkline')
        ->click('แม่แบบรายการตรวจ')
        ->assertPathIs('/templates')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('แม่แบบรายการตรวจ')
        ->assertSee('แม่แบบที่ใช้งานจริงในแต่ละรอบตรวจ')
        ->assertSee('มีแม่แบบใช้งานจริง')
        ->assertSee('สร้างแม่แบบ')
        ->assertPresent('tr[data-template-active="true"]')
        ->click('ผู้ใช้งาน')
        ->assertPathIs('/users')
        ->assertSee('บัญชีผู้ใช้งานในระบบ')
        ->assertSee('ความครอบคลุมของแต่ละบทบาท')
        ->assertSee('สร้างผู้ใช้งาน')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->click('สร้างผู้ใช้งาน')
        ->assertPathIs('/users/create')
        ->assertSee('สร้างบัญชีผู้ใช้งาน')
        ->assertSee('ไม่มีขั้นตอนส่งอีเมลเชิญในหน้านี้')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->click('แม่แบบรายการตรวจ')
        ->assertPathIs('/templates')
        ->click('สร้างแม่แบบ')
        ->assertPathIs('/templates/create')
        ->assertSee('ตรวจแบบร่าง')
        ->assertSee('ตัวอย่างตอนใช้งานจริง')
        ->assertSee('คำนิยามหลัก')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('guest desktop screenshot baselines hold for home and login', function () {
    [$homePage, $loginPage] = visit(['/', '/login']);

    stabilizeVisualState($homePage)
        ->assertNoSmoke()
        ->assertScreenshotMatches();

    stabilizeVisualState($loginPage)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();
});

test('guest mobile browser coverage holds for home and login', function () {
    $homePage = visit('/')->on()->mobile();
    $loginPage = visit('/login')->on()->mobile();

    stabilizeVisualState($homePage)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertScreenshotMatches();

    stabilizeVisualState($loginPage)
        ->assertNoSmoke()
        ->assertScreenshotMatches();
});

test('admin governance screenshot and accessibility baselines hold for deterministic authenticated surfaces', function () {
    $admin = $this->createUserForRole(UserRole::Admin, ['name' => 'Browser Governance Admin']);
    $this->createUserForRole(UserRole::Supervisor, ['name' => 'Browser Governance Supervisor']);
    $this->createUserForRole(UserRole::Staff, ['name' => 'Browser Governance Staff']);

    $this->createTemplateWithItems([
        'title' => 'Browser governance opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $dashboardPage = visit('/login');

    $dashboardPage->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoSmoke();

    $uiGovernancePage = visit('/ui-governance');

    stabilizeVisualState($uiGovernancePage)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertSee('คู่มือคุมสัญญาหน้าจอ')
        ->assertSee('เช็กลิสต์ QA ของหน้าจอ')
        ->assertScreenshotMatches();

    $templatePage = visit('/templates');

    $templatePage
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertSee('แม่แบบรายการตรวจ')
        ->assertSee('แม่แบบที่ใช้งานจริงในแต่ละรอบตรวจ');

    $userPage = visit('/users');

    $userPage
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->assertSee('บัญชีผู้ใช้งานในระบบ')
        ->assertSee('ความครอบคลุมของแต่ละบทบาท');
});

test('dashboard smoke coverage holds for desktop and mobile', function () {
    $admin = $this->createUserForRole(UserRole::Admin, ['name' => 'Dashboard Snapshot Admin']);
    $supervisor = $this->createUserForRole(UserRole::Supervisor, ['name' => 'Dashboard Snapshot Supervisor']);

    $this->createTemplateWithItems([
        'title' => 'Dashboard snapshot opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $this->createIncidentWithActivity($admin, [
        'title' => 'Dashboard snapshot hotspot incident',
        'category' => 'เครือข่าย',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
    ]);

    $this->createIncidentWithActivity($supervisor, [
        'title' => 'Dashboard snapshot overdue follow-up',
        'category' => 'ความปลอดภัย',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
        'owner_id' => $supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoSmoke();

    $dashboardDesktop = visit('/dashboard')->on()->desktop();
    $dashboardMobile = visit('/dashboard')->on()->mobile();

    stabilizeVisualState($dashboardDesktop)
        ->assertNoSmoke()
        ->assertSee('กระดานงานห้องแล็บของวันนี้')
        ->assertSee('หมวดปัญหาที่มีภาระสูง');

    stabilizeVisualState($dashboardMobile)
        ->assertNoSmoke()
        ->assertSee('กระดานงานห้องแล็บของวันนี้')
        ->assertSee('หมวดปัญหาที่มีภาระสูง');
});

test('template authoring smoke coverage holds for desktop and mobile', function () {
    $admin = $this->createUserForRole(UserRole::Admin, ['name' => 'Template Snapshot Admin']);
    $this->createTemplateWithItems([
        'title' => 'Template snapshot opening checklist',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoSmoke();

    $templateDesktop = visit('/templates/create')->on()->desktop();
    $templateMobile = visit('/templates/create')->on()->mobile();

    stabilizeVisualState($templateDesktop)
        ->assertNoSmoke()
        ->assertSee('จัดทำแม่แบบที่ใช้งานจริงใน 3 ขั้นตอน')
        ->assertSee('ภาพรวมแบบร่าง');

    stabilizeVisualState($templateMobile)
        ->assertNoSmoke()
        ->assertSee('จัดทำแม่แบบที่ใช้งานจริงใน 3 ขั้นตอน')
        ->assertSee('ภาพรวมแบบร่าง');
});

test('checklist runtime screenshot baselines hold for desktop and mobile', function () {
    $staff = $this->createUserForRole(UserRole::Staff, ['name' => 'Checklist Snapshot Staff']);
    $this->createRoom(['name' => 'Checklist Snapshot Lab', 'code' => 'LAB-QA1']);

    $this->createTemplateWithItems([
        'title' => 'Checklist snapshot template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    visit('/login')
        ->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->assertNoSmoke();

    $checklistDesktop = visit('/checklists/runs/today')->on()->desktop();
    $checklistMobile = visit('/checklists/runs/today')->on()->mobile();

    stabilizeVisualState($checklistDesktop)
        ->assertNoSmoke()
        ->assertSee('ความคืบหน้า')
        ->assertSee('ความคืบหน้าของวันนี้')
        ->assertScreenshotMatches();

    stabilizeVisualState($checklistMobile)
        ->assertNoSmoke()
        ->assertSee('ความคืบหน้า')
        ->assertSee('ความคืบหน้าของวันนี้')
        ->assertScreenshotMatches();
});

test('incident detail smoke coverage holds for desktop and mobile', function () {
    $supervisor = $this->createUserForRole(UserRole::Supervisor, ['name' => 'Incident Snapshot Supervisor']);

    $incident = $this->createIncidentWithActivity($supervisor, [
        'title' => 'Incident snapshot detail record',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::InProgress->value,
    ]);

    visit('/login')
        ->fill('email', $supervisor->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertNoSmoke();

    $incidentDesktop = visit(route('incidents.show', $incident))->on()->desktop();
    $incidentMobile = visit(route('incidents.show', $incident))->on()->mobile();

    stabilizeVisualState($incidentDesktop)
        ->assertNoSmoke()
        ->assertSee('รายละเอียดและหลักฐาน')
        ->assertSee('กำหนดผู้รับผิดชอบและวันติดตาม');

    stabilizeVisualState($incidentMobile)
        ->assertNoSmoke()
        ->assertSee('รายละเอียดและหลักฐาน')
        ->assertSee('กำหนดผู้รับผิดชอบและวันติดตาม');
});

test('management dashboard drill-down links lead to filtered incident follow-up views', function () {
    $supervisor = $this->createUserForRole(UserRole::Supervisor);

    $this->createIncidentWithActivity($supervisor, [
        'title' => 'High severity browser incident',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
    ]);

    $page = visit('/login');

    $page->fill('email', $supervisor->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertSee('ภาพรวมการทำงาน')
        ->click('ดูปัญหาความรุนแรงสูง')
        ->assertPathBeginsWith('/incidents')
        ->assertSee('ตัวกรองที่กำลังใช้งาน:')
        ->assertSee('ยังไม่ปิดเท่านั้น')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management incident queue shows accountability filters and fields without browser smoke issues', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $supervisor = $this->createUserForRole(UserRole::Supervisor);

    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser owned accountability incident',
        'status' => IncidentStatus::InProgress->value,
        'owner_id' => $admin->id,
        'follow_up_due_at' => today()->addDay(),
    ]);

    $this->createIncidentWithActivity($supervisor, [
        'title' => 'Browser overdue accountability incident',
        'status' => IncidentStatus::Open->value,
        'owner_id' => $supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->click('คิวปัญหา')
        ->assertPathIs('/incidents')
        ->assertSee('เฉพาะปัญหาที่ไม่มีผู้รับผิดชอบ')
        ->assertSee('เฉพาะปัญหาที่ฉันรับผิดชอบ')
        ->assertSee('เฉพาะรายการติดตามเกินกำหนด')
        ->assertSee('ผู้รับผิดชอบ')
        ->assertSee('กำหนดติดตาม')
        ->assertNoAccessibilityIssues()
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management can browse incident history slices without browser smoke issues', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $owner = $this->createUserForRole(UserRole::Supervisor, ['name' => 'Browser History Owner']);

    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser incident history open record',
        'status' => IncidentStatus::Open->value,
        'severity' => IncidentSeverity::High->value,
        'owner_id' => $owner->id,
        'created_at' => now()->subDay(),
    ]);

    $this->createIncidentWithActivity($owner, [
        'title' => 'Browser incident history resolved record',
        'status' => IncidentStatus::Resolved->value,
        'severity' => IncidentSeverity::Medium->value,
        'owner_id' => $owner->id,
        'created_at' => now()->subDays(2),
        'resolved_at' => now()->subDay(),
    ]);

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->click('ประวัติรายงานปัญหา')
        ->assertPathIs('/incidents/history')
        ->assertSee('ประวัติรายงานปัญหา')
        ->assertSee('การเคลื่อนไหวของรายงานปัญหาล่าสุด')
        ->assertSee('ยังไม่ปิด')
        ->assertSee('Browser incident history open record')
        ->assertSee('Browser incident history resolved record')
        ->assertNoAccessibilityIssues()
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management can browse checklist run archive without browser smoke issues', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $operator = $this->createUserForRole(UserRole::Staff, ['name' => 'Archive Browser Operator']);

    $template = $this->createTemplateWithItems([
        'title' => 'Browser archive template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ], [
        ['title' => 'Unlock room', 'description' => 'Prepare lab', 'group_label' => 'Opening checks'],
        ['title' => 'Check projector', 'description' => 'Verify display', 'group_label' => 'Opening checks'],
    ]);

    $this->createRunForUser(
        $operator,
        $template,
        submitted: true,
        itemStates: [
            ['result' => 'Done', 'note' => null],
            ['result' => 'Not Done', 'note' => 'Lamp issue'],
        ],
        runDate: now()->subDay()->toDateString(),
    );

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->click('ประวัติรอบการตรวจเช็ก')
        ->assertPathIs('/checklists/history')
        ->assertSee('วันที่ตรวจ')
        ->assertSee('Browser archive template')
        ->assertSee('ดูรอบอื่นของผู้ตรวจคนนี้')
        ->assertSee('ดูสรุปรอบตรวจ')
        ->click('ดูสรุปรอบตรวจ')
        ->assertPathBeginsWith('/checklists/history/')
        ->assertSee('สรุปประวัติย้อนหลัง')
        ->assertSee('มีจุดที่ต้องติดตาม')
        ->assertSee('พิมพ์สรุปรอบตรวจ')
        ->assertSee('ดูวันเดียวกัน')
        ->assertSee('ดูรอบตรวจเดียวกัน')
        ->assertSee('ดูผู้ตรวจคนเดิม')
        ->assertSee('Opening checks')
        ->assertSee('Lamp issue')
        ->assertNoAccessibilityIssues()
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management incident detail exposes printable evidence summary without browser smoke issues', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $owner = $this->createUserForRole(UserRole::Supervisor, ['name' => 'Printable Browser Owner']);

    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser printable issue record',
        'status' => IncidentStatus::InProgress->value,
        'severity' => IncidentSeverity::High->value,
        'owner_id' => $owner->id,
        'follow_up_due_at' => today()->addDay(),
    ]);

    visit('/login')
        ->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->click('คิวปัญหา')
        ->assertPathIs('/incidents')
        ->click('ดูรายละเอียด')
        ->assertSee('พิมพ์สรุปรายงาน')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management dashboard shows trend and hotspot sections without browser smoke issues', function () {
    $admin = $this->createUserForRole(UserRole::Admin);
    $supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->createTemplateWithItems([
        'title' => 'Opening browser scope template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);
    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser hotspot incident one',
        'category' => 'เครือข่าย',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
    ]);
    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser hotspot incident two',
        'category' => 'เครือข่าย',
        'severity' => IncidentSeverity::Medium->value,
        'status' => IncidentStatus::InProgress->value,
    ]);
    $this->createIncidentWithActivity($admin, [
        'title' => 'Browser unowned accountability incident',
        'category' => 'อื่น ๆ',
        'severity' => IncidentSeverity::Medium->value,
        'status' => IncidentStatus::Open->value,
        'owner_id' => null,
    ]);
    $this->createIncidentWithActivity($supervisor, [
        'title' => 'Browser overdue accountability pressure',
        'category' => 'ความปลอดภัย',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::Open->value,
        'owner_id' => $supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    $page = visit('/login');

    $page->fill('email', $admin->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->assertSee('รายการตรวจเช็กแยกตามรอบเวลา')
        ->assertSee('กลุ่มงานและความรับผิดชอบ')
        ->assertSee('ภาพรวมการทำงาน')
        ->assertSee('แนวโน้มรายการตรวจเช็ก')
        ->assertSee('แนวโน้มการรับรายงานปัญหา')
        ->assertSee('หมวดปัญหาที่มีภาระสูง')
        ->assertSee('ดูกลุ่มปัญหาที่ไม่มีผู้รับผิดชอบ')
        ->assertSee('ดูกลุ่มที่เลยกำหนดติดตาม')
        ->assertPresent('svg.ops-arc')
        ->assertPresent('svg.ops-sparkline')
        ->assertPresent('[data-meter-target]')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('management incident detail reads as a narrative surface without browser smoke issues', function () {
    $supervisor = $this->createUserForRole(UserRole::Supervisor);

    $incident = $this->createIncidentWithActivity($supervisor, [
        'title' => 'Narrative incident detail smoke',
        'severity' => IncidentSeverity::High->value,
        'status' => IncidentStatus::InProgress->value,
    ]);

    $incident->activities()->create([
        'action_type' => 'next_action_note',
        'summary' => 'Next action: Review this incident through the narrative surface.',
        'actor_id' => $supervisor->id,
        'created_at' => now()->addMinutes(20),
    ]);

    visit('/login')
        ->fill('email', $supervisor->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/dashboard')
        ->click('คิวปัญหา')
        ->assertPathIs('/incidents')
        ->click('ดูรายละเอียด')
        ->assertSee('ข้อมูลสำคัญ')
        ->assertSee('รายละเอียดและหลักฐาน')
        ->assertSee('กำหนดผู้รับผิดชอบและวันติดตาม')
        ->assertSee('อัปเดตสถานะอย่างมีบริบท')
        ->assertPresent('select[wire\\:model="ownerId"]')
        ->assertPresent('input[wire\\:model="followUpDueAt"]')
        ->assertPresent('[data-severity="High"]')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('staff can authenticate into the daily checklist workflow without browser smoke issues', function () {
    $staff = $this->createUserForRole(UserRole::Staff);
    $this->createRoom(['name' => 'Staff Browser Lab', 'code' => 'LAB-QA2']);

    $this->createTemplateWithItems([
        'title' => 'Staff browser smoke template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $page = visit('/login');

    $page->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs()
        ->assertPresent('body > [data-flux-main]')
        ->assertPresent('#main-content[data-flux-main]')
        ->assertSee('รายการตรวจเช็กประจำวัน')
        ->assertSee('ความคืบหน้า')
        ->assertSee('ความคืบหน้าของวันนี้')
        ->assertSee('แจ้งรายงานปัญหา')
        ->assertSee('ส่งรายการตรวจเช็ก');
});

test('staff incident reporting shows a post-submit outcome screen', function () {
    $staff = $this->createUserForRole(UserRole::Staff);
    $room = $this->createRoom([
        'name' => 'Browser Room 1',
        'code' => 'LAB-B1',
    ]);

    $page = visit('/login');

    $page->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->click('แจ้งรายงานปัญหา')
        ->assertPathIs('/incidents/new')
        ->fill('title', 'Browser smoke outcome incident')
        ->select('category', 'อื่น ๆ')
        ->assertSee($room->name)
        ->select('severity', 'Low')
        ->fill('description', 'Testing the incident outcome surface.')
        ->click('ส่งรายงานปัญหา')
        ->assertSee('สรุปการส่งรายงาน')
        ->assertSee('ขั้นตอนถัดไป')
        ->assertPresent('.ops-recap-panel')
        ->assertSee('แจ้งรายงานปัญหาอีกใบ')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});

test('staff can choose a room before entering the live checklist lane', function () {
    $staff = $this->createUserForRole(UserRole::Staff, ['name' => 'Room Choice Staff']);

    $this->createRoom([
        'name' => 'Browser Lab 1',
        'code' => 'LAB-B1',
    ]);

    $this->createRoom([
        'name' => 'Browser Lab 2',
        'code' => 'LAB-B2',
    ]);

    $this->createTemplateWithItems([
        'title' => 'Browser room choice opening template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    visit('/login')
        ->fill('email', $staff->email)
        ->fill('password', 'password')
        ->click('[data-test="login-button"]')
        ->assertPathIs('/checklists/runs/today')
        ->assertSee('เริ่มจากเลือกห้องที่จะตรวจวันนี้')
        ->assertSee('Browser Lab 1')
        ->assertSee('Browser Lab 2')
        ->click('เลือกห้องนี้')
        ->assertPathBeginsWith('/checklists/runs/today')
        ->assertSee('Browser Lab 1')
        ->assertSee('ส่งรายการตรวจเช็ก')
        ->assertNoJavaScriptErrors()
        ->assertNoConsoleLogs();
});
