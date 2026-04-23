# Program 2 / Phase 2.5 - Selective Query and Performance Hygiene

วันที่: 23 เมษายน 2026

## Scope

รอบนี้เป็น `selective query/performance hardening only`
โดยตั้งใจเก็บ debt ที่คุ้มที่สุดจาก query paths ปัจจุบัน
และหลีกเลี่ยงการเปิด refactor wave กว้าง ๆ หรือ optimization theater

## Why This Phase Comes Next

หลัง Program 2 Phase 2.1 ถึง 2.4
repo มี product truth และ workflow invariants ที่นิ่งขึ้นพอจะทำ performance hygiene แบบแคบได้แล้ว

จุดที่คุ้มสุดในรอบนี้คือ:
- dashboard snapshot query ยังมี aggregate หลายก้อนที่พึ่ง `DATE(...)`
- checklist/incident summary query ยังมี date filtering ที่ไม่ค่อยเป็นมิตรกับ index
- schema ยังไม่มี supporting indexes บางตัวสำหรับ filter/order paths ที่ใช้จริงใน dashboard และ incident follow-up views

## What Was Tightened

- `GetDashboardSnapshot` ลด query fan-out ของ checklist summary จากหลาย count queries เหลือ aggregate summary ก้อนเดียว
- dashboard incident summary เปลี่ยนจาก `DATE(...)` comparison ไปเป็น explicit day-range comparison สำหรับ intake metrics
- overdue follow-up summary เลิกครอบ field ด้วย `DATE(...)` และเทียบกับ day boundary ตรง ๆ
- checklist completion series เปลี่ยนให้ใช้ sargable date-window filter และคง `DATE(run_date)` ไว้เฉพาะจุด normalization สำหรับ series aggregation
- incident intake series เปลี่ยน start filter เป็น datetime range ที่ชัดขึ้น
- write-side date hygiene ถูก tighten ให้ `run_date` ถูกเขียนเป็นค่า date จริงใน initialization path และ seeded baseline
- เพิ่ม indexes แบบ selective ให้รองรับ:
  - checklist dashboard date/submission summary
  - incident recent ordering
  - unresolved ownership/follow-up filters

## Files Changed

- [GetDashboardSnapshot.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Dashboard/Queries/GetDashboardSnapshot.php)
- [InitializeDailyRun.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Checklists/Actions/InitializeDailyRun.php)
- [2026_04_23_000005_add_selective_query_hygiene_indexes.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/database/migrations/2026_04_23_000005_add_selective_query_hygiene_indexes.php)
- [DatabaseSeeder.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/database/seeders/DatabaseSeeder.php)
- [GetDashboardSnapshotQueryTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Feature/Application/GetDashboardSnapshotQueryTest.php)

## What Was Intentionally Left Untouched

- no schema/domain redesign
- no caching layer
- no Redis wave
- no cross-repo performance benchmark program
- no speculative query abstraction split
- no Option B machine/asset work

## Engineering Notes

- รอบนี้ตั้งใจ optimize เฉพาะ query paths ที่มีหลักฐานใน repo และเอกสารก่อนหน้า
- ยังไม่ claim ว่าระบบผ่าน load/perf testing แล้ว
- index set ถูกคุมให้แคบ เพื่อไม่เพิ่ม write cost แบบไม่จำเป็น
- dashboard query object ยังเป็น hotspot ที่ควรเฝ้าต่อ แต่ยังไม่แตก class เพิ่มแบบ over-engineer ในรอบนี้

## Verification

- `php artisan test tests/Feature/Application/GetDashboardSnapshotQueryTest.php`
- `php artisan test tests/Feature/DashboardTest.php`
- `php artisan test tests/Feature/Application/ListIncidentsQueryTest.php`
- `composer lint:check`

## Brutal Truth

รอบนี้ไม่ได้ทำให้ระบบกลายเป็น high-scale platform
แต่มันทำให้ query paths ที่สำคัญที่สุดใน product ปัจจุบัน
มีฐานที่สะอาดขึ้น, index-friendly ขึ้น, และพร้อมต่อยอดโดยไม่ใช้วิธี quick-and-dirty
