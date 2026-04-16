<?php

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Livewire\Admin\ChecklistTemplates\Manage;
use App\Models\ChecklistTemplate;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = $this->createUserForRole(UserRole::Admin);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->staff = $this->createUserForRole(UserRole::Staff);
    $this->activeTemplate = $this->createTemplateWithItems([
        'title' => 'Baseline active template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ], [
        ['title' => 'Active item 1', 'description' => 'Baseline active item 1', 'group_label' => 'Opening checks'],
        ['title' => 'Active item 2', 'description' => 'Baseline active item 2', 'group_label' => 'Safety checks'],
    ]);
});

test('admin can access checklist templates inside the main application shell', function () {
    $response = $this->actingAs($this->admin)->get(route('templates.index'));

    $response->assertOk();
    $response->assertSee('Checklist Templates');
    $response->assertSee('Create template');
    $response->assertSee('Baseline active template');
});

test('non-admin users cannot access checklist template administration routes', function () {
    $this->actingAs($this->supervisor)->get(route('templates.index'))->assertForbidden();
    $this->actingAs($this->staff)->get(route('templates.index'))->assertForbidden();
    $this->actingAs($this->supervisor)->post(route('templates.duplicate', $this->activeTemplate))->assertForbidden();
    $this->actingAs($this->staff)->post(route('templates.duplicate', $this->activeTemplate))->assertForbidden();
});

test('admin can create a checklist template and making it active retires existing templates', function () {
    $existingActive = $this->activeTemplate;

    Livewire::actingAs($this->admin)
        ->test(Manage::class)
        ->set('title', 'ตรวจระหว่างวัน')
        ->set('description', 'ตรวจความพร้อมระหว่างชั่วโมงใช้งาน')
        ->set('scope', ChecklistScope::MIDDAY->value)
        ->set('is_active', true)
        ->set('items', [
            [
                'id' => null,
                'title' => 'ตรวจอุณหภูมิห้อง',
                'description' => 'ให้แน่ใจว่าสภาพแวดล้อมยังเหมาะสม',
                'group_label' => 'Environment',
                'sort_order' => 1,
                'is_required' => true,
            ],
            [
                'id' => null,
                'title' => 'ตรวจจุดเสี่ยงด้านความปลอดภัย',
                'description' => 'ตรวจสายไฟและทางเดิน',
                'group_label' => 'Safety',
                'sort_order' => 2,
                'is_required' => true,
            ],
        ])
        ->call('save')
        ->assertHasNoErrors();

    $newTemplate = ChecklistTemplate::query()->where('title', 'ตรวจระหว่างวัน')->firstOrFail();

    expect($newTemplate->is_active)->toBeTrue();
    expect($newTemplate->items()->count())->toBe(2);
    expect($existingActive->fresh()->is_active)->toBeFalse();
});

test('admin can update an existing template and its checklist items', function () {
    $template = ChecklistTemplate::query()->create([
        'title' => 'เทมเพลตทดลอง',
        'description' => 'ยังไม่ถูกใช้งาน',
        'scope' => ChecklistScope::CLOSING->value,
        'is_active' => false,
    ]);

    $firstItem = $template->items()->create([
        'title' => 'รายการเดิม 1',
        'description' => 'คำอธิบายเดิม',
        'sort_order' => 1,
        'is_required' => true,
    ]);

    $template->items()->create([
        'title' => 'รายการเดิม 2',
        'description' => 'จะถูกลบออก',
        'sort_order' => 2,
        'is_required' => true,
    ]);

    Livewire::actingAs($this->admin)
        ->test(Manage::class, ['template' => $template])
        ->set('title', 'เปิดห้องปฏิบัติการเวอร์ชันปรับปรุง')
        ->set('description', 'รายละเอียดใหม่สำหรับ flow เปิดห้อง')
        ->set('is_active', false)
        ->set('items', [
            [
                'id' => $firstItem->id,
                'title' => 'เปิดไฟและทดสอบไฟส่องสว่าง',
                'description' => 'อัปเดตรายการเดิม',
                'group_label' => 'Facility setup',
                'sort_order' => 2,
                'is_required' => true,
            ],
            [
                'id' => null,
                'title' => 'ตรวจระบบล็อกประตู',
                'description' => 'รายการใหม่',
                'group_label' => 'Security',
                'sort_order' => 1,
                'is_required' => true,
            ],
        ])
        ->call('save')
        ->assertHasNoErrors();

    $template->refresh()->load('items');

    expect($template->title)->toBe('เปิดห้องปฏิบัติการเวอร์ชันปรับปรุง');
    expect($template->is_active)->toBeFalse();
    expect($template->items()->count())->toBe(2);
    expect($template->items()->where('title', 'ตรวจระบบล็อกประตู')->exists())->toBeTrue();
    expect($template->items()->where('title', 'เปิดไฟและทดสอบไฟส่องสว่าง')->exists())->toBeTrue();
    expect($template->items()->where('title', 'ตรวจระบบล็อกประตู')->first()?->group_label)->toBe('Security');
});

test('admin can duplicate a checklist template into an inactive editable copy', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('templates.duplicate', $this->activeTemplate));

    $duplicate = ChecklistTemplate::query()
        ->where('title', 'Baseline active template (Copy)')
        ->firstOrFail();

    $response->assertRedirect(route('templates.edit', $duplicate));
    $response->assertSessionHas('message', 'Checklist template duplicated. Review the copy, then activate it when ready.');

    expect($duplicate->is_active)->toBeFalse();
    expect($duplicate->scope)->toBe($this->activeTemplate->scope);
    expect($duplicate->items()->count())->toBe($this->activeTemplate->items()->count());
    expect($duplicate->items()->orderBy('sort_order')->first()?->group_label)->toBe('Opening checks');
});

test('duplicating the same checklist template more than once increments the copy title safely', function () {
    $this->actingAs($this->admin)->post(route('templates.duplicate', $this->activeTemplate));
    $this->actingAs($this->admin)->post(route('templates.duplicate', $this->activeTemplate));

    expect(ChecklistTemplate::query()->where('title', 'Baseline active template (Copy)')->exists())->toBeTrue();
    expect(ChecklistTemplate::query()->where('title', 'Baseline active template (Copy 2)')->exists())->toBeTrue();
});

test('used checklist items cannot be removed from templates with run history', function () {
    $template = $this->createTemplateWithItems([
        'title' => 'Template with run history',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => false,
    ], [
        ['title' => 'Historic item 1', 'description' => 'Historic item 1'],
        ['title' => 'Historic item 2', 'description' => 'Historic item 2'],
    ])->load('items');

    $operator = $this->createUserForRole(UserRole::Staff);
    $this->createRunForUser($operator, $template, submitted: true);
    $firstItem = $template->items->sortBy('sort_order')->values()->first();

    Livewire::actingAs($this->admin)
        ->test(Manage::class, ['template' => $template])
        ->set('items', [
            [
                'id' => $firstItem->id,
                'title' => $firstItem->title,
                'description' => $firstItem->description,
                'sort_order' => 1,
                'is_required' => true,
            ],
        ])
        ->call('save')
        ->assertHasErrors(['items']);
});

test('legacy admin urls are retired from the public route contract', function () {
    $template = $this->activeTemplate;

    $this->actingAs($this->admin)
        ->get('/admin')
        ->assertNotFound();

    $this->actingAs($this->admin)
        ->get('/admin/checklist-templates')
        ->assertNotFound();

    $this->actingAs($this->admin)
        ->get('/admin/checklist-templates/create')
        ->assertNotFound();

    $this->actingAs($this->admin)
        ->get("/admin/checklist-templates/{$template->id}/edit")
        ->assertNotFound();
});

test('admin login route is not exposed as a separate entry point', function () {
    $this->get('/admin/login')->assertNotFound();
});

test('database forbids creating a second active checklist template directly', function () {
    ChecklistTemplate::query()->where('is_active', true)->firstOrFail();

    expect(fn () => ChecklistTemplate::query()->create([
        'title' => 'เทมเพลต active ซ้ำ',
        'description' => 'ต้องถูกปฏิเสธโดย persistence invariant',
        'scope' => ChecklistScope::MIDDAY->value,
        'is_active' => true,
    ]))->toThrow(QueryException::class);
});

test('database forbids duplicate checklist template titles', function () {
    expect(fn () => ChecklistTemplate::query()->create([
        'title' => $this->activeTemplate->title,
        'description' => 'ชื่อซ้ำต้องไม่ผ่าน',
        'scope' => ChecklistScope::MIDDAY->value,
        'is_active' => false,
    ]))->toThrow(QueryException::class);
});
