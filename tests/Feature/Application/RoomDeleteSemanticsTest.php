<?php

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\Incident;
use App\Models\Room;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->operator = $this->createUserForRole(UserRole::Staff);
    $this->room = $this->createRoom([
        'name' => 'Lab Protected 1',
        'code' => 'LAB-PROT-01',
    ]);
    $this->template = $this->createTemplateWithItems([
        'title' => 'Protected room template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);
});

test('database forbids deleting a room that still owns checklist history', function () {
    $this->createRunForUser(
        $this->operator,
        $this->template,
        submitted: true,
        room: $this->room,
    );

    expect(fn () => $this->room->delete())->toThrow(QueryException::class);
    expect(Room::query()->whereKey($this->room->id)->exists())->toBeTrue();
});

test('database forbids deleting a room that still owns incident history', function () {
    Incident::query()->create([
        'title' => 'Protected room incident',
        'category' => 'อื่น ๆ',
        'severity' => 'Medium',
        'room_id' => $this->room->id,
        'status' => 'Open',
        'description' => 'Incident keeps the room in historical use.',
        'created_by' => $this->operator->id,
    ]);

    expect(fn () => $this->room->delete())->toThrow(QueryException::class);
    expect(Room::query()->whereKey($this->room->id)->exists())->toBeTrue();
});

test('database still allows deleting an unused room', function () {
    $unusedRoom = $this->createRoom([
        'name' => 'Lab Disposable 1',
        'code' => 'LAB-DISP-01',
    ]);

    expect(fn () => $unusedRoom->delete())->not->toThrow(QueryException::class);
    expect(Room::query()->whereKey($unusedRoom->id)->exists())->toBeFalse();
});
