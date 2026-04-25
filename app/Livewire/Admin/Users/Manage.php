<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Application\Users\Actions\CreateManagedUser;
use App\Application\Users\Actions\UpdateManagedUser;
use App\Application\Users\Support\UserRosterSummaryBuilder;
use App\Domain\Access\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Manage extends Component
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $role = '';

    public bool $is_active = true;

    public string $password = '';

    public string $password_confirmation = '';

    /** @var list<string> */
    public array $roles = [];

    public function mount(?User $user = null): void
    {
        $this->roles = UserRole::values();
        $this->user = $user;

        if (! $user) {
            $this->role = UserRole::Staff->value;

            return;
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->is_active = $user->is_active;
    }

    public function save(CreateManagedUser $createManagedUser, UpdateManagedUser $updateManagedUser): void
    {
        $this->resetErrorBag();

        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ];

        try {
            $managedUser = $this->user
                ? $updateManagedUser($this->user, $payload, auth()->user())
                : $createManagedUser($payload, auth()->user());
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());

            return;
        }

        session()->flash('message', $this->user
            ? 'อัปเดตบัญชีผู้ใช้งานเรียบร้อยแล้ว'
            : 'สร้างบัญชีผู้ใช้งานเรียบร้อยแล้ว');

        $this->redirectRoute('users.edit', $managedUser, navigate: true);
    }

    public function getPageTitleProperty(): string
    {
        return $this->user ? 'แก้ไขบัญชีผู้ใช้งาน' : 'สร้างบัญชีผู้ใช้งาน';
    }

    public function getPageDescriptionProperty(): string
    {
        return $this->user
            ? 'ปรับบทบาท สิทธิ์การเข้าใช้งาน และการตั้งรหัสผ่านได้จากหน้าจัดการหลักของระบบ'
            : 'สร้างบัญชีภายในใหม่พร้อมกำหนดบทบาท สถานะการใช้งาน และรหัสผ่านเริ่มต้นอย่างชัดเจน';
    }

    /**
     * @return list<array{
     *     tone: 'info'|'warning'|'success',
     *     title: string,
     *     body: string
     * }>
     */
    public function getLifecycleSignalsProperty(): array
    {
        $signals = [];

        if ($this->role === UserRole::Admin->value) {
            $signals[] = [
                'tone' => 'warning',
                'title' => 'บทบาทผู้ดูแลระบบเป็นสิทธิ์ระดับกำกับดูแล',
                'body' => 'ผู้ดูแลระบบสามารถจัดการแม่แบบ ผู้ใช้งาน และหน้าควบคุมที่มีการป้องกันได้ จึงควรกำหนดบทบาทนี้อย่างตั้งใจ',
            ];
        }

        if (! $this->is_active) {
            $signals[] = [
                'tone' => 'warning',
                'title' => 'บัญชีที่ปิดใช้งานจะเข้าสู่ระบบไม่ได้',
                'body' => 'เมื่อปิดการใช้งาน บัญชีจะถูกบล็อกทันทีตอนยืนยันตัวตน ควรใช้สถานะนี้กับบัญชีที่ตั้งใจปิดจริง ไม่ใช่กรณีที่ยังไม่แน่ใจ',
            ];
        }

        if ($this->user && auth()->id() === $this->user->id) {
            $signals[] = [
                'tone' => 'info',
                'title' => 'คุณกำลังแก้ไขบัญชีของตนเอง',
                'body' => 'หน้าจอนี้มีข้อจำกัดเพื่อป้องกันการลดสิทธิ์หรือปิดการใช้งานบัญชีผู้ดูแลระบบของตนเอง คุณยังแก้ข้อมูลส่วนตัวและตั้งรหัสผ่านใหม่ได้ตามปกติ',
            ];
        }

        if ($this->user && filled($this->password)) {
            $signals[] = [
                'tone' => 'info',
                'title' => 'การบันทึกครั้งนี้จะเปลี่ยนรหัสผ่านปัจจุบัน',
                'body' => 'หากต้องการแก้เฉพาะบทบาท อีเมล หรือสถานะการใช้งาน ให้เว้นช่องรหัสผ่านว่างไว้',
            ];
        }

        if (! $this->user) {
            $signals[] = [
                'tone' => 'success',
                'title' => 'การสร้างบัญชีอยู่ภายใต้การควบคุมภายใน',
                'body' => 'การสร้างบัญชีทำได้จากผู้ดูแลระบบเท่านั้น ไม่มีการส่งคำเชิญหรือเปิดให้สมัครใช้งานสาธารณะจากหน้าจอนี้',
            ];
        }

        return $signals === []
            ? [[
                'tone' => 'success',
                'title' => 'การตั้งค่าบัญชีอยู่ในสภาพพร้อมใช้งาน',
                'body' => 'บัญชีนี้สามารถปรับปรุงได้จากภายในระบบอย่างปลอดภัย โดยไม่ต้องย้อนกลับไปแก้ฐานข้อมูลด้วยมือ',
            ]]
            : $signals;
    }

    public function getBlocksSelfAdminRoleChangeProperty(): bool
    {
        return (bool) ($this->user && auth()->id() === $this->user->id && auth()->user()?->isAdmin());
    }

    public function getPasswordHandoffNoteProperty(): string
    {
        return $this->user
            ? 'ใช้ส่วนนี้เมื่อผู้ดูแลระบบต้องตั้งรหัสผ่านใหม่ให้ผู้ใช้งานโดยตรง แล้วส่งต่อผ่านช่องทางสื่อสารภายในที่ทีมใช้งานจริง'
            : 'กำหนดรหัสผ่านเริ่มต้นจากหน้านี้ แล้วส่งต่อผ่านกระบวนการภายในของทีม หน้านี้ไม่ได้พึ่งการส่งอีเมลเชิญ';
    }

    /**
     * @return list<array{
     *     role: string,
     *     title: string,
     *     description: string,
     *     total_count: int,
     *     active_count: int,
     *     inactive_count: int,
     *     state: 'covered'|'warning',
     *     is_selected_role: bool
     * }>
     */
    public function getRoleGovernanceProperty(): array
    {
        return collect(app(UserRosterSummaryBuilder::class)()['role_lanes'])
            ->map(function (array $lane): array {
                $lane['is_selected_role'] = $lane['role'] === $this->role;

                return $lane;
            })
            ->all();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.users.manage', [
            'rosterSummary' => app(UserRosterSummaryBuilder::class)(),
        ]);
    }
}
