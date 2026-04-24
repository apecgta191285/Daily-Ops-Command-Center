# Integrity and QA Closure Fix Execution Pack

วันที่: 24 เมษายน 2026

## Scope

รอบนี้เป็น corrective hardening pass แบบแคบ
เพื่อปิด defect จริงที่ตรวจพบหลังการ review เชิงลึกของ Program 2 work:

- database invariant ของ room-aware checklist runs หลุดจากระดับฐานข้อมูล
- checklist history รับ `runDate` แบบ malformed แล้วเสี่ยงพา route ไปสู่ 500
- browser suite ไม่ตรงกับ current UI truth และไม่อยู่ในสถานะ green

## What Was Fixed

- คืน room-aware checklist run uniqueness กลับไปที่ฐานข้อมูลด้วย migration ใหม่
- เพิ่ม preflight guard ใน migration เพื่อไม่ซ่อน duplicate-data problem ถ้ามีอยู่จริง
- sanitize `runDate` filter ให้ invalid input ถูกลดเหลือ empty filter แทนที่จะ parse ตรง
- เพิ่ม regression proof สำหรับ malformed checklist history date input
- อัปเดต browser checklist tests ให้สะท้อน room-centered setup และ current UI wording จริง
- อัปเดต screenshot baselines เฉพาะจอที่เปลี่ยนโดยเจตนา

## Files Changed

- `database/migrations/2026_04_24_000001_restore_room_aware_checklist_run_uniqueness.php`
- `app/Application/Checklists/Data/ChecklistRunHistoryFilters.php`
- `app/Application/Checklists/Queries/ListChecklistRunHistory.php`
- `app/Livewire/Management/Checklists/HistoryIndex.php`
- `tests/Feature/Application/InitializeDailyRunActionTest.php`
- `tests/Feature/ChecklistRunHistoryTest.php`
- `tests/Browser/SmokeTest.php`
- snapshot files under `tests/.pest/snapshots/Browser/SmokeTest`

## What Was Intentionally Left Untouched

- no schema redesign beyond invariant restoration
- no dashboard/query redesign wave
- no product scope expansion
- no Option B work
- no broad browser coverage redesign

## Engineering Note

รอบนี้ตั้งใจแก้ที่ root cause:
- invariant ที่ควรอยู่ใน DB ต้องกลับไปอยู่ใน DB
- user input ที่มาจาก URL ต้องถูก normalize ก่อนถึง query layer
- browser QA ต้องพูดความจริงกับ UI ปัจจุบัน ไม่ใช่กับ wording เก่า
