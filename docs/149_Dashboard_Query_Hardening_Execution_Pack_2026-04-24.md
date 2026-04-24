# Dashboard Query Hardening Execution Pack

Date: 2026-04-24  
Scope: `Priority 4 — Selective Dashboard Query Hardening`

## 1. Why This Round Exists

จาก full-stack audit dashboard ยังเป็น hotspot ที่ชัดที่สุดใน repo:

- query object เดียวถือ aggregate หลายก้อนเกินไป
- incident summary logic และ hotspot logic อัดอยู่ใน class เดียว
- raw aggregate concentration สูงเกินไปสำหรับ read path ที่สำคัญที่สุดจอหนึ่งของระบบ

Brutal truth:

- มันยังทำงานได้
- แต่ maintainability และ ownership ยังไม่คมพอ
- รอบนี้จึงเก็บเฉพาะก้อนที่คุ้มที่สุดก่อน ไม่เปิด refactor wave ใหญ่

## 2. Changes Landed

### 2.1 Added a dedicated incident-summary owner

เพิ่ม [DashboardIncidentSummaryBuilder.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Dashboard/Support/DashboardIncidentSummaryBuilder.php)

owner นี้รับผิดชอบ:

- status counts
- unresolved follow-up pressure counts
- today/yesterday intake counts
- hotspot rows

### 2.2 Thinned the dashboard query object

ใน [GetDashboardSnapshot.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Dashboard/Queries/GetDashboardSnapshot.php)

จากเดิม:

- query object ถือ incident summary raw aggregate ทั้งก้อน
- hotspot query อยู่ใน class เดียวกัน

เป็น:

- `GetDashboardSnapshot` ทำหน้าที่ orchestration มากขึ้น
- incident summary และ hotspot rows ถูก delegate ไปที่ dedicated builder

### 2.3 Kept the scope narrow

รอบนี้ตั้งใจ `ไม่แตะ`:

- checklist completion series
- incident intake series 7-day chart
- recent archive/history context
- dashboard UI
- caching/metrics/perf infrastructure

## 3. Regression Proof Added

เพิ่ม [DashboardIncidentSummaryBuilderTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Unit/DashboardIncidentSummaryBuilderTest.php)

สิ่งที่ยืนยัน:

- incident summary counts ยังถูกต้อง
- hotspot rows ยัง order ตาม unresolved count
- resolved incidents ไม่หลุดเข้า unresolved hotspot rows

และยัง rerun:

- [GetDashboardSnapshotQueryTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Feature/Application/GetDashboardSnapshotQueryTest.php)
- full app test suite

## 4. What Improved

- dashboard query ownership คมขึ้น
- read path หลักของ dashboard อ่านง่ายขึ้น
- hotspot risk ถูกกดลงในมิติ maintainability แม้ยังไม่ใช่ full performance rewrite

## 5. Remaining Truth

- dashboard ยังเป็น important read path ที่มี aggregate เยอะ
- แต่ตอนนี้ไม่ถูกอัดแน่นอยู่ใน query object เดียวเท่าเดิมแล้ว
- ถ้าจะเดินต่ออย่างถูกลำดับที่สุดหลังรอบนี้ ควรเป็น remaining read-surface pagination / authorization depth / legacy attachment backfill อย่างใดอย่างหนึ่ง ไม่ใช่เปิด feature wave ใหม่
