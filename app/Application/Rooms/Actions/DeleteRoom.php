<?php

declare(strict_types=1);

namespace App\Application\Rooms\Actions;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class DeleteRoom
{
    public function __invoke(Room $room, User $actor): void
    {
        $this->ensureAdmin($actor);
        $this->ensureNoOperationalHistory($room);

        $room->delete();
    }

    private function ensureAdmin(User $actor): void
    {
        if (! $actor->isAdmin()) {
            throw new AuthorizationException('Only administrators can manage rooms.');
        }
    }

    private function ensureNoOperationalHistory(Room $room): void
    {
        if ($room->checklistRuns()->exists() || $room->incidents()->exists()) {
            throw ValidationException::withMessages([
                'room' => 'ห้องนี้มีประวัติการตรวจหรือรายงานปัญหาแล้ว จึงลบไม่ได้ ให้ปิดใช้งานแทนเพื่อรักษาประวัติระบบ',
            ]);
        }
    }
}
