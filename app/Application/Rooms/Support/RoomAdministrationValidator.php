<?php

declare(strict_types=1);

namespace App\Application\Rooms\Support;

use App\Models\Room;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoomAdministrationValidator
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array{name: string, code: string, description: string|null, is_active: bool}
     */
    public function validateCreate(array $attributes): array
    {
        return $this->validate($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{name: string, code: string, description: string|null, is_active: bool}
     */
    public function validateUpdate(Room $room, array $attributes): array
    {
        return $this->validate($attributes, $room);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{name: string, code: string, description: string|null, is_active: bool}
     */
    private function validate(array $attributes, ?Room $room = null): array
    {
        $prepared = [
            ...$attributes,
            'name' => trim((string) ($attributes['name'] ?? '')),
            'code' => Str::upper(trim((string) ($attributes['code'] ?? ''))),
            'description' => filled($attributes['description'] ?? null)
                ? trim((string) $attributes['description'])
                : null,
        ];

        /** @var array{name: string, code: string, description: string|null, is_active: bool} $validated */
        $validated = Validator::make($prepared, [
            'name' => ['required', 'string', 'max:120'],
            'code' => [
                'required',
                'string',
                'max:40',
                'regex:/^[A-Z0-9][A-Z0-9._-]*$/',
                Rule::unique('rooms', 'code')->ignore($room?->getKey()),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['required', 'boolean'],
        ], [
            'code.regex' => 'รหัสห้องต้องขึ้นต้นด้วยตัวอักษรหรือตัวเลข และใช้ได้เฉพาะ A-Z, 0-9, จุด, ขีดกลาง หรือขีดล่าง',
        ])->validate();

        return [
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'is_active' => (bool) $validated['is_active'],
        ];
    }
}
