<?php

namespace Database\Seeders;

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\ChecklistItem;
use App\Models\ChecklistRun;
use App\Models\ChecklistRunItem;
use App\Models\ChecklistTemplate;
use App\Models\Incident;
use App\Models\IncidentActivity;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with exact Day 2B locked demo data.
     */
    public function run(): void
    {
        // 1. Users (4 records)
        $defaultPassword = Hash::make('password');

        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Lab Admin',
            'password' => $defaultPassword,
            'role' => UserRole::Admin->value,
            'is_active' => true,
        ]);

        $supervisor = User::firstOrCreate(['email' => 'supervisor@example.com'], [
            'name' => 'Lab Supervisor',
            'password' => $defaultPassword,
            'role' => UserRole::Supervisor->value,
            'is_active' => true,
        ]);

        $operatorA = User::firstOrCreate(['email' => 'operatora@example.com'], [
            'name' => 'Duty Staff A',
            'password' => $defaultPassword,
            'role' => UserRole::Staff->value,
            'is_active' => true,
        ]);

        $operatorB = User::firstOrCreate(['email' => 'operatorb@example.com'], [
            'name' => 'Duty Staff B',
            'password' => $defaultPassword,
            'role' => UserRole::Staff->value,
            'is_active' => true,
        ]);

        // 2. Rooms (5 records)
        $rooms = collect([
            ['code' => 'LAB-01', 'name' => 'Lab 1', 'description' => 'Primary teaching computer lab'],
            ['code' => 'LAB-02', 'name' => 'Lab 2', 'description' => 'General practice lab'],
            ['code' => 'LAB-03', 'name' => 'Lab 3', 'description' => 'Project and presentation lab'],
            ['code' => 'LAB-04', 'name' => 'Lab 4', 'description' => 'Overflow student lab'],
            ['code' => 'LAB-05', 'name' => 'Lab 5', 'description' => 'Special session lab'],
        ])->map(fn (array $room) => Room::firstOrCreate(
            ['code' => $room['code']],
            [
                'name' => $room['name'],
                'description' => $room['description'],
                'is_active' => true,
            ],
        ));

        // 3. Checklist Templates (2 records)
        $t1 = ChecklistTemplate::firstOrCreate(['title' => 'เปิดห้องปฏิบัติการ'], [
            'description' => 'ตรวจความพร้อมก่อนเริ่มใช้งาน',
            'scope' => ChecklistScope::OPENING->value,
            'is_active' => true,
        ]);

        $t2 = ChecklistTemplate::firstOrCreate(['title' => 'ปิดห้องปฏิบัติการ'], [
            'description' => 'ตรวจความเรียบร้อยก่อนปิดพื้นที่',
            'scope' => ChecklistScope::CLOSING->value,
            'is_active' => false,
        ]);

        // 4. Checklist Items (12 records)
        $t1Items = [
            'เปิดไฟและตรวจสภาพไฟส่องสว่าง' => 'ต้องมั่นใจว่าพื้นที่พร้อมใช้งาน',
            'เปิดเครื่องคอมพิวเตอร์ตัวอย่าง 1 เครื่อง' => 'ตรวจว่าอุปกรณ์หลักใช้งานได้',
            'ตรวจการเชื่อมต่ออินเทอร์เน็ต' => 'ใช้ยืนยันความพร้อมของเครือข่าย',
            'ตรวจโปรเจกเตอร์หรือจอแสดงผลหลัก' => 'ใช้ในกรณีมีการสอนหรือสาธิต',
            'ตรวจความสะอาดโต๊ะและทางเดิน' => 'ป้องกันสภาพพื้นที่ไม่พร้อม',
            'ตรวจว่าพื้นที่ไม่มีสายไฟหรือสิ่งกีดขวางผิดปกติ' => 'เป็น checklist ด้านความปลอดภัยพื้นฐาน',
        ];
        $order = 1;
        foreach ($t1Items as $title => $desc) {
            ChecklistItem::firstOrCreate([
                'checklist_template_id' => $t1->id,
                'sort_order' => $order,
            ], [
                'title' => $title,
                'description' => $desc,
                'is_required' => true,
            ]);
            $order++;
        }

        $t2Items = [
            'ตรวจว่าผู้ใช้งานออกจากพื้นที่แล้ว' => 'ป้องกันการปิดพื้นที่ขณะยังมีผู้ใช้งาน',
            'ปิดเครื่องคอมพิวเตอร์และอุปกรณ์หลัก' => 'ลดการใช้พลังงานและความเสี่ยง',
            'เก็บอุปกรณ์ที่วางผิดที่ให้เรียบร้อย' => 'รักษาความพร้อมของพื้นที่วันถัดไป',
            'ตรวจความสะอาดพื้นฐานของโต๊ะและทางเดิน' => 'ลดงานค้างสะสม',
            'ปิดไฟ/แอร์/อุปกรณ์ที่ไม่จำเป็น' => 'ใช้เป็นขั้นตอนปิดพื้นที่',
            'ตรวจและล็อกประตูหรือแจ้งผู้รับผิดชอบ' => 'เป็นขั้นตอนความปลอดภัยพื้นฐาน',
        ];
        $order = 1;
        foreach ($t2Items as $title => $desc) {
            ChecklistItem::firstOrCreate([
                'checklist_template_id' => $t2->id,
                'sort_order' => $order,
            ], [
                'title' => $title,
                'description' => $desc,
                'is_required' => true,
            ]);
            $order++;
        }

        // 5. Checklist Runs & Checklist Run Items
        $today = Carbon::today()->format('Y-m-d 00:00:00');
        $yesterday = Carbon::yesterday()->format('Y-m-d 00:00:00');
        $lab1 = $rooms[0];
        $lab2 = $rooms[1];
        $lab3 = $rooms[2];
        $lab4 = $rooms[3];
        $lab5 = $rooms[4];

        $run1 = ChecklistRun::updateOrCreate([
            'checklist_template_id' => $t1->id,
            'run_date' => $today,
            'created_by' => $operatorA->id,
        ], [
            'room_id' => $lab1->id,
            'assigned_team_or_scope' => ChecklistScope::OPENING->value,
            'submitted_at' => Carbon::now(),
            'submitted_by' => $operatorA->id,
        ]);

        $run2 = ChecklistRun::updateOrCreate([
            'checklist_template_id' => $t2->id,
            'run_date' => $today,
            'created_by' => $operatorB->id,
        ], [
            'room_id' => $lab2->id,
            'assigned_team_or_scope' => ChecklistScope::CLOSING->value,
        ]);

        $run3 = ChecklistRun::updateOrCreate([
            'checklist_template_id' => $t1->id,
            'run_date' => $yesterday,
            'created_by' => $operatorB->id,
        ], [
            'room_id' => $lab3->id,
            'assigned_team_or_scope' => ChecklistScope::OPENING->value,
            'submitted_at' => Carbon::yesterday()->setTime(9, 10),
            'submitted_by' => $operatorB->id,
        ]);

        // Items for run 1 (Done/Submitted)
        foreach ($t1->items as $item) {
            ChecklistRunItem::firstOrCreate([
                'checklist_run_id' => $run1->id,
                'checklist_item_id' => $item->id,
            ], [
                'result' => ChecklistResult::Done->value,
                'checked_by' => $operatorA->id,
                'checked_at' => Carbon::now(),
            ]);
        }

        // Items for run 2 (Pending/Not Submitted)
        foreach ($t2->items as $item) {
            ChecklistRunItem::firstOrCreate([
                'checklist_run_id' => $run2->id,
                'checklist_item_id' => $item->id,
            ], []);
        }

        foreach ($t1->items->values() as $index => $item) {
            ChecklistRunItem::firstOrCreate([
                'checklist_run_id' => $run3->id,
                'checklist_item_id' => $item->id,
            ], [
                'result' => $index === 1 ? ChecklistResult::NotDone->value : ChecklistResult::Done->value,
                'note' => $index === 1 ? 'Printer check failed during opening round.' : null,
                'checked_by' => $operatorB->id,
                'checked_at' => Carbon::yesterday()->setTime(9, 5),
            ]);
        }

        // 6. Incidents (10 records)
        $incidentsData = [
            ['title' => 'เครื่อง PC-03 เปิดไม่ติด', 'category' => 'อุปกรณ์คอมพิวเตอร์', 'severity' => 'Medium', 'status' => IncidentStatus::Open->value, 'days_ago' => 0, 'room_id' => $lab1->id, 'equipment_reference' => 'PC-03'],
            ['title' => 'อินเทอร์เน็ตใช้งานไม่ได้ทั้งห้อง', 'category' => 'เครือข่าย', 'severity' => 'High', 'status' => IncidentStatus::InProgress->value, 'days_ago' => 3, 'room_id' => $lab2->id, 'equipment_reference' => 'Core Switch Lab 2'],
            ['title' => 'โปรเจกเตอร์ภาพเบลอ', 'category' => 'อุปกรณ์คอมพิวเตอร์', 'severity' => 'Medium', 'status' => IncidentStatus::Resolved->value, 'days_ago' => 4, 'room_id' => $lab3->id, 'equipment_reference' => 'Projector Front'],
            ['title' => 'โต๊ะด้านหลังมีฝุ่นมาก', 'category' => 'ความสะอาด', 'severity' => 'Low', 'status' => IncidentStatus::Resolved->value, 'days_ago' => 2, 'room_id' => $lab1->id, 'equipment_reference' => null],
            ['title' => 'สายไฟใต้โต๊ะวางระเกะระกะ', 'category' => 'ความปลอดภัย', 'severity' => 'High', 'status' => IncidentStatus::Open->value, 'days_ago' => 5, 'room_id' => $lab4->id, 'equipment_reference' => 'Plug A3'],
            ['title' => 'แอร์ห้องไม่เย็น', 'category' => 'สภาพแวดล้อม', 'severity' => 'Medium', 'status' => IncidentStatus::InProgress->value, 'days_ago' => 1, 'room_id' => $lab5->id, 'equipment_reference' => 'Air Conditioner'],
            ['title' => 'เมาส์เครื่อง PC-07 ขัดข้อง', 'category' => 'อุปกรณ์คอมพิวเตอร์', 'severity' => 'Low', 'status' => IncidentStatus::Resolved->value, 'days_ago' => 6, 'room_id' => $lab2->id, 'equipment_reference' => 'PC-07 Mouse'],
            ['title' => 'ปลั๊กพ่วงใกล้หน้าห้องมีรอยไหม้', 'category' => 'ความปลอดภัย', 'severity' => 'High', 'status' => IncidentStatus::Open->value, 'days_ago' => 4, 'room_id' => $lab3->id, 'equipment_reference' => 'Front Power Strip'],
            ['title' => 'พื้นทางเดินมีขยะและสาย LAN พาด', 'category' => 'ความสะอาด', 'severity' => 'Medium', 'status' => IncidentStatus::Open->value, 'days_ago' => 2, 'room_id' => $lab4->id, 'equipment_reference' => null],
            ['title' => 'เสียงพัดลมเครื่อง PC-02 ดังผิดปกติ', 'category' => 'อุปกรณ์คอมพิวเตอร์', 'severity' => 'Low', 'status' => IncidentStatus::InProgress->value, 'days_ago' => 1, 'room_id' => $lab5->id, 'equipment_reference' => 'PC-02'],
        ];

        foreach ($incidentsData as $data) {
            $incident = Incident::updateOrCreate(['title' => $data['title']], [
                'category' => $data['category'],
                'severity' => $data['severity'],
                'room_id' => $data['room_id'],
                'status' => $data['status'],
                'description' => 'รายละเอียดจำลองสำหรับสภาวะ: '.$data['title'],
                'equipment_reference' => $data['equipment_reference'],
                'created_by' => $operatorA->id,
                'resolved_at' => $data['status'] === IncidentStatus::Resolved->value ? Carbon::now() : null,
            ]);

            $createdAt = Carbon::now()->subDays($data['days_ago'])->setTime(9 + ($data['days_ago'] % 4), 15);
            $incident->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $data['status'] === IncidentStatus::Resolved->value
                    ? $createdAt->copy()->addHours(6)
                    : $createdAt->copy()->addHours(2),
                'resolved_at' => $data['status'] === IncidentStatus::Resolved->value
                    ? $createdAt->copy()->addHours(6)
                    : null,
            ])->saveQuietly();

            IncidentActivity::firstOrCreate([
                'incident_id' => $incident->id,
                'action_type' => 'created',
                'summary' => 'Incident reported',
            ], [
                'actor_id' => $operatorA->id,
                'created_at' => $createdAt,
            ]);

            if ($data['status'] !== IncidentStatus::Open->value) {
                IncidentActivity::firstOrCreate([
                    'incident_id' => $incident->id,
                    'action_type' => 'status_changed',
                    'summary' => "Status updated to {$data['status']}",
                ], [
                    'actor_id' => $supervisor->id,
                    'created_at' => $createdAt->copy()->addHours(6),
                ]);
            }
        }
    }
}
