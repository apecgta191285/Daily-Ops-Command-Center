# Program 2 / Phase 2.6 - Query Boundary and History Path Hardening

วันที่: 23 เมษายน 2026

## Scope

รอบนี้เป็น `query-boundary completion only`
ต่อจาก Phase 2.5 โดยเก็บ query paths ที่ยังเหลือ `whereDate(...)` หรือยังขาด index support ใน read flows ที่ใช้งานจริง

## Why This Phase Comes Next

หลัง Phase 2.5 dashboard aggregate path ถูก tighten แล้ว
แต่ยังเหลือ query paths ที่มีผลกับ runtime/history surfaces โดยตรง:
- staff daily scope board
- dashboard scope lanes
- checklist history filters
- incident history slices

ดังนั้นรอบนี้จึงเป็นการปิด tail ของ query hygiene wave
ไม่ใช่การเปิด optimization program ใหม่

## What Was Tightened

- `BuildDailyScopeBoard` เปลี่ยนจาก `whereDate(run_date)` ไปเป็น direct date equality บนคอลัมน์ `date`
- `DashboardScopeLaneBuilder` เปลี่ยนจาก `whereDate(run_date)` ไปเป็น direct date equality
- `ListChecklistRunHistory` เปลี่ยน run-date filter ให้เทียบกับค่า date ตรง ๆ
- `ListIncidentHistorySlices` เปลี่ยน history window จาก date-wrapper comparison ไปเป็น explicit datetime lower-bound comparison
- `InitializeDailyRun` เปลี่ยน current-run lookup จาก equality-based `firstOrCreate` ไปเป็น explicit day-window lookup แล้วค่อย create เมื่อไม่พบ
- test/data helpers ถูก tighten ให้ `run_date` ถูกสร้างจาก date intent ที่ชัดขึ้น
- เพิ่ม supporting indexes สำหรับ:
  - daily scope board by operator/date/room
  - dashboard scope lane summary by run date/scope/submission state
  - incident history resolution window

## Files Changed

- [BuildDailyScopeBoard.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Checklists/Queries/BuildDailyScopeBoard.php)
- [DashboardScopeLaneBuilder.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Dashboard/Support/DashboardScopeLaneBuilder.php)
- [ListChecklistRunHistory.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Checklists/Queries/ListChecklistRunHistory.php)
- [ListIncidentHistorySlices.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Incidents/Queries/ListIncidentHistorySlices.php)
- [InitializeDailyRun.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Checklists/Actions/InitializeDailyRun.php)
- [2026_04_23_000006_add_history_and_scope_board_indexes.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/database/migrations/2026_04_23_000006_add_history_and_scope_board_indexes.php)
- [ChecklistRunFactory.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/database/factories/ChecklistRunFactory.php)
- [CreatesApplicationScenarios.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/CreatesApplicationScenarios.php)
- [ChecklistDailyRunTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Feature/ChecklistDailyRunTest.php)
- [IncidentHistoryTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Feature/IncidentHistoryTest.php)

## What Was Intentionally Left Untouched

- no caching or memoization layer
- no schema redesign
- no benchmark harness
- no queue/reporting architecture work
- no speculative repository abstraction

## Engineering Notes

- รอบนี้ยังคงหลัก `evidence before optimization`
- index additions ถูกจำกัดไว้เฉพาะ read paths ที่มีใน product ปัจจุบันจริง
- บาง query ถูกเก็บเป็น day-window แทน equality เพื่อรองรับ legacy rows ที่ถูก persist เป็น `YYYY-MM-DD 00:00:00` ใน SQLite path เดิม
- การ harden นี้ยังไม่เท่ากับ performance certification หรือ load-test proof
- เป้าหมายคือทำให้ query semantics ชัด, predictable, และ friendly กับฐานข้อมูลมากขึ้น

## Verification

- `php artisan test tests/Feature/ChecklistDailyRunTest.php`
- `php artisan test tests/Feature/IncidentHistoryTest.php`
- `php artisan test tests/Feature/DashboardTest.php`
- `php artisan test tests/Feature/Application/GetDashboardSnapshotQueryTest.php`
- `composer lint:check`

## Brutal Truth

รอบนี้ไม่ได้เพิ่ม feature ใหม่
แต่มันช่วยให้ daily/history read paths ของระบบ
ไม่ต้องพึ่ง date-wrapping queries แบบหลวม ๆ
และทำให้ฐาน query hygiene ของ Program 2 แน่นขึ้นก่อนขยับไปโปรแกรมถัดไป
