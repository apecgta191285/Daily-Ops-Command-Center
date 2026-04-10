<?php

namespace Database\Seeders;

use App\Models\ChecklistItem;
use App\Models\ChecklistRun;
use App\Models\ChecklistRunItem;
use App\Models\ChecklistTemplate;
use App\Models\Incident;
use App\Models\IncidentActivity;
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
            'role' => 'admin',
            'is_active' => true,
        ]);

        $supervisor = User::firstOrCreate(['email' => 'supervisor@example.com'], [
            'name' => 'Lab Supervisor',
            'password' => $defaultPassword,
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        $operatorA = User::firstOrCreate(['email' => 'operatora@example.com'], [
            'name' => 'Operator A',
            'password' => $defaultPassword,
            'role' => 'staff',
            'is_active' => true,
        ]);

        $operatorB = User::firstOrCreate(['email' => 'operatorb@example.com'], [
            'name' => 'Operator B',
            'password' => $defaultPassword,
            'role' => 'staff',
            'is_active' => true,
        ]);

        // 2. Checklist Templates (2 records)
        $t1 = ChecklistTemplate::firstOrCreate(['title' => 'เปิดห้องปฏิบัติการ'], [
            'description' => 'ตรวจความพร้อมก่อนเริ่มใช้งาน',
            'scope' => 'เปิดห้อง',
            'is_active' => true,
        ]);

        $t2 = ChecklistTemplate::firstOrCreate(['title' => 'ปิดห้องปฏิบัติการ'], [
            'description' => 'ตรวจความเรียบร้อยก่อนปิดพื้นที่',
            'scope' => 'ปิดห้อง',
            'is_active' => false,
        ]);

        // 3. Checklist Items (12 records)
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

        // 4. Checklist Runs (2 records min) & 5. Checklist Run Items (12 records)
        $today = Carbon::today()->format('Y-m-d 00:00:00');

        $run1 = ChecklistRun::firstOrCreate([
            'checklist_template_id' => $t1->id,
            'run_date' => $today,
            'created_by' => $operatorA->id,
        ], [
            'assigned_team_or_scope' => 'เปิดห้อง',
            'submitted_at' => Carbon::now(),
            'submitted_by' => $operatorA->id,
        ]);

        $run2 = ChecklistRun::firstOrCreate([
            'checklist_template_id' => $t2->id,
            'run_date' => $today,
            'created_by' => $operatorB->id,
        ], [
            'assigned_team_or_scope' => 'ปิดห้อง',
        ]);

        // Items for run 1 (Done/Submitted)
        foreach ($t1->items as $item) {
            ChecklistRunItem::firstOrCreate([
                'checklist_run_id' => $run1->id,
                'checklist_item_id' => $item->id,
            ], [
                'result' => 'Done',
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

        // 6. Incidents (10 records)
        $incidentsData = [
            ['title' => 'เครื่อง PC-03 เปิดไม่ติด', 'category' => 'อุปกรณ์คอมพิวเตอร์', 'severity' => 'Medium', 'status' => 'Open'],
            ['title' => 'อินเทอร์เน็ตใช้งานไม่ได้ทั้งห้อง', 'category' => 'เครือข่าย', 'severity' => 'High', 'status' => 'In Progress'],
            ['title' => 'โปรเจกเตอร์ภาพเบลอ', 'category' => 'อุปกรณ์คอมพิวเตอร์', 'severity' => 'Medium', 'status' => 'Resolved'],
            ['title' => 'โต๊ะด้านหลังมีฝุ่นมาก', 'category' => 'ความสะอาด', 'severity' => 'Low', 'status' => 'Resolved'],
            ['title' => 'สายไฟใต้โต๊ะวางระเกะระกะ', 'category' => 'ความปลอดภัย', 'severity' => 'High', 'status' => 'Open'],
            ['title' => 'แอร์ห้องไม่เย็น', 'category' => 'สภาพแวดล้อม', 'severity' => 'Medium', 'status' => 'In Progress'],
            ['title' => 'เมาส์เครื่อง PC-07 ขัดข้อง', 'category' => 'อุปกรณ์คอมพิวเตอร์', 'severity' => 'Low', 'status' => 'Resolved'],
            ['title' => 'ปลั๊กพ่วงใกล้หน้าห้องมีรอยไหม้', 'category' => 'ความปลอดภัย', 'severity' => 'High', 'status' => 'Open'],
            ['title' => 'พื้นทางเดินมีขยะและสาย LAN พาด', 'category' => 'ความสะอาด', 'severity' => 'Medium', 'status' => 'Open'],
            ['title' => 'เสียงพัดลมเครื่อง PC-02 ดังผิดปกติ', 'category' => 'อุปกรณ์คอมพิวเตอร์', 'severity' => 'Low', 'status' => 'In Progress'],
        ];

        foreach ($incidentsData as $data) {
            $incident = Incident::firstOrCreate(['title' => $data['title']], [
                'category' => $data['category'],
                'severity' => $data['severity'],
                'status' => $data['status'],
                'description' => 'รายละเอียดจำลองสำหรับสภาวะ: '.$data['title'],
                'created_by' => $operatorA->id,
                'resolved_at' => $data['status'] === 'Resolved' ? Carbon::now() : null,
            ]);

            IncidentActivity::firstOrCreate([
                'incident_id' => $incident->id,
                'action_type' => 'created',
                'summary' => 'Incident reported',
            ], [
                'actor_id' => $operatorA->id,
                'created_at' => Carbon::now(),
            ]);

            if ($data['status'] !== 'Open') {
                IncidentActivity::firstOrCreate([
                    'incident_id' => $incident->id,
                    'action_type' => 'status_changed',
                    'summary' => "Status updated to {$data['status']}",
                ], [
                    'actor_id' => $supervisor->id,
                    'created_at' => Carbon::now()->addMinutes(10),
                ]);
            }
        }
    }
}
