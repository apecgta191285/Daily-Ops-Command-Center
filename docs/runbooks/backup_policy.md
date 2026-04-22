# Backup Policy
วันที่: 23 เมษายน 2026

## 1) Scope
policy นี้ใช้กับ `production v1 single-node baseline` เท่านั้น

ครอบคลุม:
- MySQL application database
- attachment files บน `storage/app/public/incidents`

ไม่ครอบคลุม:
- multi-region backup
- object storage lifecycle
- cross-cloud DR

## 2) Backup Targets
ต้อง backup อย่างน้อย:

### Database
- users
- rooms
- checklist templates
- checklist runs
- incidents
- incident activity / supporting application tables
- queue/cache/session tables ตาม production baseline

### File Attachments
- `storage/app/public/incidents`
- symlink metadata ไม่ต้อง backup ถ้า recreate ได้ แต่ target files ต้อง backup

## 3) Minimum Backup Cadence
baseline ขั้นต่ำที่ phase นี้แนะนำ:

- database:
  - daily full backup
  - additional pre-release checkpoint ก่อน release เสี่ยง
- attachments:
  - daily backup หรือ equivalent snapshot
  - additional pre-release checkpoint ถ้า release แตะ attachment/storage path

## 4) Retention Baseline
pragmatic v1 retention:
- daily backups เก็บอย่างน้อย 7 วัน
- weekly backup checkpoint เก็บอย่างน้อย 4 สัปดาห์

หมายเหตุ:
- ถ้าหน่วยงานมี policy ภายในที่เข้มกว่า ให้ใช้ policy ภายในแทน
- phase นี้ยังไม่ claim ว่า retention strategy ปิดสมบูรณ์สำหรับ long-term compliance

## 5) RPO / RTO Baseline
baseline เชิง pragmatic สำหรับ v1:
- target RPO: ไม่เกิน 24 ชั่วโมง
- target RTO: ภายในวันทำการเดียวกัน ถ้า recovery owner พร้อม

Brutal truth:
- ค่านี้เป็น `working baseline`
- ยังไม่ใช่ performance promise
- ต้องมี restore drill จริงก่อนจึงจะพูดอย่างมั่นใจได้มากขึ้น

## 6) Backup Ownership
ต้องระบุ owner ให้ชัด:
- ใครสั่ง backup
- ใครตรวจว่า backup สำเร็จ
- ใครเข้าถึง backup artifacts ได้
- ใครอนุมัติ restore

ถ้ายังตอบไม่ได้ phase นี้ยังไม่ปิดจริง

## 7) Storage and Access Rules
- backup artifacts ต้องไม่อยู่ใน repo
- backup artifacts ต้องไม่อยู่เฉพาะบน working directory ของแอป
- access ต้องจำกัดเฉพาะ operator/owner ที่ได้รับอนุญาต
- credentials ที่ใช้ backup ต้องไม่ hardcode ใน repo

## 8) Pre-Release Backup Rule
ก่อน release ที่มี migration หรือ release risk สูง:
- ต้องมี backup checkpoint ล่าสุด
- ต้องรู้ว่า backup artifact อยู่ที่ไหน
- ต้องรู้ว่า restore owner คือใคร

## 9) Known Limitation
policy นี้อย่างเดียวไม่พอ

ถ้ายังไม่มี:
- restore runbook
- restore drill evidence
- owner confirmation

ก็ยังไม่ควรอ้างว่า recovery readiness พร้อมแล้ว
