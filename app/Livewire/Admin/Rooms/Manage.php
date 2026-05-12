<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Rooms;

use App\Application\Rooms\Actions\CreateRoom;
use App\Application\Rooms\Actions\DeleteRoom;
use App\Application\Rooms\Actions\UpdateRoom;
use App\Application\Rooms\Support\RoomLifecycleSummaryBuilder;
use App\Models\Room;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Manage extends Component
{
    public ?Room $room = null;

    public string $name = '';

    public string $code = '';

    public ?string $description = null;

    public bool $is_active = true;

    public function mount(?Room $room = null): void
    {
        $this->room = $room;

        if (! $room) {
            return;
        }

        $this->name = $room->name;
        $this->code = $room->code;
        $this->description = $room->description;
        $this->is_active = $room->is_active;
    }

    public function save(CreateRoom $createRoom, UpdateRoom $updateRoom): void
    {
        $this->resetErrorBag();

        $payload = [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        try {
            $managedRoom = $this->room
                ? $updateRoom($this->room, $payload, auth()->user())
                : $createRoom($payload, auth()->user());
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());

            return;
        }

        session()->flash('message', $this->room
            ? 'อัปเดตห้องเรียบร้อยแล้ว'
            : 'สร้างห้องเรียบร้อยแล้ว');

        $this->redirectRoute('rooms.edit', $managedRoom, navigate: true);
    }

    public function delete(DeleteRoom $deleteRoom): void
    {
        if (! $this->room) {
            return;
        }

        $this->resetErrorBag();

        try {
            $deleteRoom($this->room, auth()->user());
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());

            return;
        }

        session()->flash('message', 'ลบห้องที่ยังไม่มีประวัติเรียบร้อยแล้ว');

        $this->redirectRoute('rooms.index', navigate: true);
    }

    public function getPageTitleProperty(): string
    {
        return $this->room ? 'แก้ไขห้อง' : 'สร้างห้อง';
    }

    public function getPageDescriptionProperty(): string
    {
        return $this->room
            ? 'ปรับชื่อ รหัส คำอธิบาย และสถานะการใช้งานของห้อง โดยคงประวัติการตรวจและรายงานปัญหาไว้ครบถ้วน'
            : 'เพิ่มห้องเข้าสู่ระบบกลาง เพื่อให้รายการตรวจ รายงานปัญหา และรายงานสรุปอ้างอิง master data ชุดเดียวกัน';
    }

    public function getHasOperationalHistoryProperty(): bool
    {
        return (bool) ($this->room && (
            $this->room->checklistRuns()->exists()
            || $this->room->incidents()->exists()
        ));
    }

    /**
     * @return list<array{tone: 'info'|'warning'|'success', title: string, body: string}>
     */
    public function getLifecycleSignalsProperty(): array
    {
        if (! $this->room) {
            return [[
                'tone' => 'success',
                'title' => 'ห้องใหม่จะถูกนำไปใช้เป็น master data ทันที',
                'body' => 'เมื่อเปิดใช้งาน ห้องนี้จะปรากฏให้ผู้ตรวจเลือกในการสร้างรอบตรวจและแจ้งรายงานปัญหา',
            ]];
        }

        if ($this->hasOperationalHistory) {
            return [[
                'tone' => 'warning',
                'title' => 'ห้องนี้มีประวัติการใช้งานแล้ว',
                'body' => 'ระบบไม่ควรลบห้องนี้ เพราะจะกระทบ audit trail ของ checklist, incident และรายงานย้อนหลัง ให้ใช้การปิดใช้งานแทน',
            ]];
        }

        if (! $this->is_active) {
            return [[
                'tone' => 'warning',
                'title' => 'ห้องนี้จะถูกซ่อนจาก workflow ใหม่',
                'body' => 'การปิดใช้งานเหมาะกับห้องที่หยุดใช้งานชั่วคราวหรือเลิกใช้แล้ว แต่ยังต้องเก็บข้อมูลไว้ในระบบ',
            ]];
        }

        return [[
            'tone' => 'success',
            'title' => 'ห้องนี้ยังไม่มีประวัติและแก้ไขได้เต็มรูปแบบ',
            'body' => 'หากสร้างผิดและยังไม่ถูกใช้งานจริง ผู้ดูแลระบบสามารถลบออกได้อย่างปลอดภัย',
        ]];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.rooms.manage', [
            'roomSummary' => app(RoomLifecycleSummaryBuilder::class)(),
        ]);
    }
}
