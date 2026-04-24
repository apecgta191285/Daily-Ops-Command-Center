# Checklist Run Archive Pagination Hygiene Execution Pack

Date: 2026-04-24  
Scope: `Priority 5 — Remaining Large Read Surface Hygiene`

## 1. Why This Round Exists

หลังเก็บ incident history และ dashboard hotspot แล้ว
จอ read surface ที่ยังชัดว่าโหลดทั้งก้อนอยู่คือ checklist run archive

Brutal truth:

- archive นี้เป็น management surface
- มันมี filter จริงและมีแนวโน้มโตตามประวัติการใช้งาน
- การใช้ `->get()` ทั้งชุดต่อไปไม่คุ้มในเชิง hygiene แล้ว

## 2. Changes Landed

### 2.1 Added pagination to checklist run archive

ใน [ListChecklistRunHistory.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Checklists/Queries/ListChecklistRunHistory.php)

- แยก `query()` ออกมาเป็น owner ชัด
- เพิ่ม `paginate()`
- คง `__invoke()` ไว้สำหรับ collection use case

### 2.2 Preserved archive day context honestly

ใน [HistoryIndex.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Livewire/Management/Checklists/HistoryIndex.php)

- ใช้ `WithPagination`
- reset page เมื่อ filter เปลี่ยน
- ไม่ใช้ current page collection มาสรุป archive context ตรงๆ
- แต่ query `focusDateRuns()` แยกตาม focus date เพื่อให้ day context ยังหมายถึง “coverage ของวันนั้น” จริง

### 2.3 Updated the archive surface

ใน [history-index.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/management/checklists/history-index.blade.php)

- ปรับ empty-state check ให้รองรับ paginator
- เพิ่ม pagination links ใต้ตาราง

## 3. Regression Proof Added

อัปเดต [ChecklistRunHistoryTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Feature/ChecklistRunHistoryTest.php)

สิ่งที่ยืนยัน:

- archive ยัง respect filters เดิม
- long result sets paginate ได้จริง
- archive day context ไม่หลุดไปวันอื่นเมื่อ filter/runDate ถูกล็อก

## 4. What Was Intentionally Left Untouched

- ไม่แตะ history detail / print recap
- ไม่แตะ dashboard/archive wording
- ไม่เปิด broad pagination wave ทุกจอพร้อมกัน
- ไม่เปิด authorization refactor

## 5. Remaining Truth

- remaining large read surfaces ลดลงอีกหนึ่งจอแล้ว
- แต่ยังมี debt คนละก้อน เช่น authorization depth และ legacy attachment backfill
- รอบถัดไปที่ถูกลำดับที่สุดควรเป็น selective authorization hardening มากกว่าทำ performance wave กว้าง
