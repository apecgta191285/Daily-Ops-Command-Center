# A-lite Daily Ops Command Center Presentation Redesign Outline

แนวทางรอบใหม่: ใช้ codebase เป็นหลักในการสรุป feature, roles, tools, routes, models และ data structure พร้อมใช้ภาพ diagram ที่อนุมัติใน repository เป็นภาพประกอบ

## Slide 1: A-lite Daily Ops Command Center

- Codebase-first presentation
- ระบบจัดการงานปฏิบัติการประจำวันสำหรับทีมดูแลห้องคอมของมหาวิทยาลัย
- 3 บทบาทผู้ใช้, 5 กระบวนการหลัก, 8 ตารางหลัก

## Slide 2: ที่มาและความสำคัญ

- งานตรวจห้องคอมเกิดซ้ำทุกวันตามช่วงเปิดห้อง ตรวจระหว่างวัน และปิดห้อง
- ระบบงานเดิมใช้กระดาษ ไฟล์แยก หรือข้อความสนทนา
- ปัญหาคือข้อมูลไม่รวมศูนย์ บริบทหน้างานไม่ชัด และทบทวนย้อนหลังยาก
- ระบบใหม่มุ่งรวม checklist, incident และ dashboard ไว้ในระบบเดียว

## Slide 3: ขอบเขตโครงงาน

- Staff: ตรวจเช็กประจำวันและแจ้งเหตุผิดปกติ
- Supervisor: ติดตามแดชบอร์ดและอัปเดตสถานะ incident
- Admin: จัดการผู้ใช้และแม่แบบ checklist
- ระบบรองรับ daily checklist, incident queue/history, dashboard, user/template management และ printable recap/summary
- ไม่รวม public signup, notification เต็มรูปแบบ, approval workflow, machine registry, analytics warehouse, mobile app แยก หรือ AI/copilot

## Slide 4: เครื่องมือที่ใช้จาก Codebase

- Backend: PHP 8.4, Laravel 13, Laravel Fortify
- Frontend: Livewire 4, Flux 2, Tailwind CSS, Vite
- Data: Laravel migrations, Eloquent models, SQLite development database
- Quality: Pest / Browser tests, Laravel Pint, Composer, NPM
- Source evidence: composer.json, package.json, routes/web.php, app/Livewire, app/Models, database/migrations, tests

## Slide 5: ก้างปลา

- ใช้ `ch3-01-cause-effect-final.png`
- เล่าปัญหาของระบบเดิมและผลกระทบ

## Slide 6: Flowchart

- ใช้ `ch3-02-system-flowchart-v2.png`
- เล่าการทำงานใหม่ตั้งแต่ Staff เลือกห้อง/ช่วงงาน จนถึง Supervisor/Admin ติดตามผ่านแดชบอร์ดและ incident queue

## Slide 7: Context Diagram

- ใช้ `ch3-03-context-diagram-v2.png`
- ระบบกลางคือ A-lite Daily Ops Command Center
- External entities มี Staff, Supervisor และ Admin เท่านั้น

## Slide 8: DFD Level 1

- ใช้ `ch3-04-dfd-level1-v3.png`
- Process หลัก: จัดการผู้ใช้, จัดการแม่แบบ, ตรวจเช็กประจำวัน, จัดการเหตุผิดปกติ, ติดตามภาพรวม

## Slide 9: DFD Level 2

- ใช้ thumbnail ของ `ch3-05`, `ch3-06`, `ch3-07`
- สรุป workflow สำคัญ 3 กระบวนการ โดยไม่อัดเส้น DFD ทั้งหมดเป็นสไลด์ใหญ่

## Slide 10: Data Model

- ใช้ `ch3-08-erd-final.png`
- 8 entities ตรงกับ models และ migrations

## Slide 11: Data Dictionary

- แบ่งตารางเป็น 3 กลุ่ม: users/rooms, checklist, incident
- ระบุว่าเล่ม Data Dictionary แสดง Attribute, Description, Type, Constraint และ Null ตาม schema truth

## Slide 12: ผลลัพธ์ที่ยืนยันจากระบบจริง

- Authentication + Roles
- Daily Checklist
- Incident Management
- Dashboard + History
- Printable Evidence

## Slide 13: สรุปสำหรับการนำเสนอ

- รวมศูนย์ข้อมูล
- ทำงานตามบทบาท
- ตรวจสอบย้อนหลังได้
- แนวทางพัฒนาต่อ: notification พื้นฐาน, การพิมพ์/ส่งออกสรุปรายงานตามช่วงเวลา, mobile-friendly workflow และ test coverage เพิ่มเติม
