# Demo Data and Flow Validation for Option A
วันที่: 23 เมษายน 2026

## 1) Validation Goal
เอกสารนี้ใช้ยืนยันว่า seeded demo state และ flow หลักของ Option A
ยังรองรับการสาธิตและการตอบคำถามได้จริงหลังจาก freeze implementation แล้ว

ขอบเขตของรอบนี้:
- validate จาก current repo truth
- validate จาก local seeded data
- แยกให้ชัดว่าอะไร `demo-ready`
- แยกให้ชัดว่าอะไรเป็น `known limitation`

เอกสารนี้ **ไม่ใช่ feature wave ใหม่**
และ **ไม่ใช่ bug-fix expansion wave**

## 2) Validation Baseline
ใช้ baseline ต่อไปนี้:
- local database reset ด้วย `php artisan migrate:fresh --seed`
- seeded users:
  - `admin@example.com`
  - `supervisor@example.com`
  - `operatora@example.com`
  - `operatorb@example.com`
- seeded rooms:
  - `Lab 1`
  - `Lab 2`
  - `Lab 3`
  - `Lab 4`
  - `Lab 5`

## 3) Demo Flow Checklist

### Step 1 — Staff Login
- ล็อกอินด้วย `operatora@example.com`
- คาดหวังว่าเข้า staff lane ได้
- คาดหวังว่า local demo wording ยังสอดคล้องกับ actor mapping ปัจจุบัน

### Step 2 — Choose Room
- ถ้ามีหลายห้อง active ระบบต้องให้เลือกห้องก่อน
- ต้องเห็นว่ากำลังจะทำ checklist ให้ห้องใด

### Step 3 — Open Checklist for Selected Room
- checklist lane ต้องผูกกับ `room + time scope`
- user ต้องไม่สับสนว่ากำลังเช็กห้องไหน

### Step 4 — Submit Checklist
- checklist run ต้องถูกสร้าง/อัปเดตโดยมี room context
- uniqueness ของ run ต้องไม่ชนกันข้ามหลายห้องในวันเดียวกัน

### Step 5 — Create Incident with Room Context
- incident create ต้องรับ room context
- incident ต้องรับ `optional equipment_reference`
- ถ้าเข้าจาก checklist flow room ต้อง prefill ถูก

### Step 6 — Supervisor Login
- ล็อกอินด้วย `supervisor@example.com`
- เข้า dashboard ได้

### Step 7 — Dashboard / Queue Review
- supervisor ต้องเห็น room context ใน dashboard และ incident queue
- ต้องใช้ room context เพื่อรู้ว่าปัญหาอยู่ห้องไหน

### Step 8 — Incident Detail / History / Print
- incident detail ต้องแสดง room context
- incident history ต้องสะท้อน room context
- checklist history / printable recap / printable summary ต้องยังใช้ได้

### Step 9 — Admin Governance
- ล็อกอินด้วย `admin@example.com`
- template governance และ user governance ต้องยังใช้งานได้ใน case study เดียวกัน

## 4) Evidence Collected in This Round

### Database Baseline
- `php artisan migrate:fresh --seed` ผ่าน
- `php artisan migrate:status` ยืนยันว่า migrations ทั้งหมดเป็น `Ran`

### Feature Proof
- `php artisan test --filter=OptionARoomDemoClosureTest` ผ่าน
- proof ที่ได้:
  - room context เดินจาก checklist ไป incident ได้จริง
  - optional equipment reference ถูกเก็บและแสดงได้จริง
  - supervisor เห็น room context ใน dashboard / queue / detail / history
  - admin governance ยังใช้งานได้ใน room-centered case study

### Browser Proof
- `./vendor/bin/pest tests/Browser/SmokeTest.php --filter='staff can choose a room before entering the live checklist lane'` ผ่าน
- `./vendor/bin/pest tests/Browser/SmokeTest.php --filter='management dashboard drill-down links lead to filtered incident follow-up views'` ผ่าน

## 5) Validation Verdict
### Result
`Option A demo flow is coherent and runnable from current seeded/local truth.`

พูดแบบตรงที่สุด:
- staff flow ใช้เดโมได้จริง
- room selection ใช้เดโมได้จริง
- room-aware incident flow ใช้เดโมได้จริง
- supervisor follow-up story ใช้เดโมได้จริง
- admin governance ยังอยู่ใน product family เดียวกันจริง

## 6) Known Limitations That Are Honest to Say
สิ่งเหล่านี้เป็นข้อจำกัดที่พูดได้ตรง ๆ ระหว่างเดโมหรือสอบ:

1. `equipment_reference` เป็นข้อความสั้นแบบ lightweight ไม่ใช่ machine registry
2. ระบบยังไม่รองรับ machine lifecycle หรือ machine history แบบเป็น entity จริง
3. dashboard เป็น room-aware workboard แต่ยังไม่ใช่ deep room/machine intelligence board
4. browser QA มี proof สำคัญแล้ว แต่ยังไม่ใช่ full visual gate ของทุก authenticated screen
5. ระบบยังไม่ควรถูกเรียกว่า production-grade platform

## 7) Demo-Blocking vs Non-Blocking

### Demo-Blocking Bugs Found
- ไม่มีในรอบนี้

### Non-Blocking Reality Notes
- ต้องเลี่ยงการใช้คำอธิบายที่ทำให้กรรมการเข้าใจว่า `equipment_reference = machine registry`
- ต้องเลี่ยงการพูดเหมือนระบบมี analytics หรือ inventory ลึกกว่าที่มีจริง

## 8) Recommended Talk Track During Demo
ใช้แกนสั้นนี้เวลาอธิบาย:

> นักศึกษาที่เข้าเวรเลือกห้องก่อน จากนั้นทำ checklist ตามช่วงเวลา ถ้าพบปัญหาก็สร้าง incident พร้อม room context และ optional equipment reference แล้วผู้ดูแลห้องจะเห็นปัญหานั้นต่อใน dashboard, queue, detail, history, และ print surfaces

## 9) Final Recommendation for Phase 2.2
ถือว่า `Phase 2.2 — Demo Data & Demo Flow Validation` ปิดได้

จากจุดนี้ไป งานถัดไปที่ถูกต้องที่สุดคือ:
- `Phase 2.3 — Thai Wording / Presentation Grounding`

ไม่ควร:
- เปิด engineering wave ใหม่
- polish UI นอกเหนือจาก presentation need
- ขยาย product scope
