<?php

declare(strict_types=1);

namespace App\Application\Rooms\Actions;

use App\Application\Rooms\Support\RoomAdministrationValidator;
use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class UpdateRoom
{
    public function __construct(
        private readonly RoomAdministrationValidator $validator,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(Room $room, array $attributes, User $actor): Room
    {
        $this->ensureAdmin($actor);

        $room->update($this->validator->validateUpdate($room, $attributes));

        return $room->fresh();
    }

    private function ensureAdmin(User $actor): void
    {
        if (! $actor->isAdmin()) {
            throw new AuthorizationException('Only administrators can manage rooms.');
        }
    }
}
