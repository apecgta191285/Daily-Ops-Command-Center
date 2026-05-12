<?php

use App\Application\Rooms\Actions\CreateRoom;
use App\Application\Rooms\Actions\DeleteRoom;
use App\Application\Rooms\Actions\UpdateRoom;
use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\Room;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = $this->createUserForRole(UserRole::Admin);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor);
    $this->staff = $this->createUserForRole(UserRole::Staff);
});

test('admin can create a room with normalized code and explicit active state', function () {
    $room = app(CreateRoom::class)([
        'name' => '  Lab Network 1  ',
        'code' => ' lab-net-01 ',
        'description' => '  Network training room  ',
        'is_active' => true,
    ], $this->admin);

    expect($room->name)->toBe('Lab Network 1')
        ->and($room->code)->toBe('LAB-NET-01')
        ->and($room->description)->toBe('Network training room')
        ->and($room->is_active)->toBeTrue();
});

test('room administration rejects duplicate and unsafe room codes', function () {
    $this->createRoom(['code' => 'LAB-01']);

    expect(fn () => app(CreateRoom::class)([
        'name' => 'Duplicate room',
        'code' => 'lab-01',
        'description' => null,
        'is_active' => true,
    ], $this->admin))->toThrow(ValidationException::class);

    expect(fn () => app(CreateRoom::class)([
        'name' => 'Unsafe room',
        'code' => 'lab 01',
        'description' => null,
        'is_active' => true,
    ], $this->admin))->toThrow(ValidationException::class);
});

test('admin can update room lifecycle without losing identity', function () {
    $room = $this->createRoom([
        'name' => 'Lab 1',
        'code' => 'LAB-01',
        'is_active' => true,
    ]);

    $updated = app(UpdateRoom::class)($room, [
        'name' => 'Lab 1 - Renovated',
        'code' => 'LAB-01A',
        'description' => 'Updated operating room',
        'is_active' => false,
    ], $this->admin);

    expect($updated->name)->toBe('Lab 1 - Renovated')
        ->and($updated->code)->toBe('LAB-01A')
        ->and($updated->description)->toBe('Updated operating room')
        ->and($updated->is_active)->toBeFalse();
});

test('non admin users cannot create update or delete rooms', function () {
    $room = $this->createRoom(['code' => 'LAB-SEC']);

    expect(fn () => app(CreateRoom::class)([
        'name' => 'Blocked room',
        'code' => 'LAB-BLOCKED',
        'description' => null,
        'is_active' => true,
    ], $this->supervisor))->toThrow(AuthorizationException::class);

    expect(fn () => app(UpdateRoom::class)($room, [
        'name' => $room->name,
        'code' => $room->code,
        'description' => $room->description,
        'is_active' => false,
    ], $this->supervisor))->toThrow(AuthorizationException::class);

    expect(fn () => app(DeleteRoom::class)($room, $this->staff))->toThrow(AuthorizationException::class);
});

test('delete room action allows unused rooms and rejects operational history', function () {
    $unusedRoom = $this->createRoom(['code' => 'LAB-UNUSED']);
    $protectedRoom = $this->createRoom(['code' => 'LAB-HISTORY']);
    $template = $this->createTemplateWithItems([
        'title' => 'Room administration protection template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $this->createRunForUser($this->staff, $template, submitted: true, room: $protectedRoom);

    app(DeleteRoom::class)($unusedRoom, $this->admin);

    expect(Room::query()->whereKey($unusedRoom->id)->exists())->toBeFalse();

    expect(fn () => app(DeleteRoom::class)($protectedRoom, $this->admin))
        ->toThrow(ValidationException::class);

    expect(Room::query()->whereKey($protectedRoom->id)->exists())->toBeTrue();
});
