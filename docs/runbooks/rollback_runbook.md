# Rollback Runbook
วันที่: 23 เมษายน 2026

## 1) Scope
runbook นี้ใช้กับ `production v1 single-node baseline`
และต้องใช้คู่กับ [deployment_runbook.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/deployment_runbook.md)

## 2) Brutal Truth
rollback ไม่ได้แปลว่า “ย้อนทุกอย่างได้ทันที”

สิ่งที่ rollback ได้ง่ายกว่า:
- app code
- frontend assets
- config cache state
- worker process state

สิ่งที่ rollback ไม่ควรถูก assume ว่าย้อนง่าย:
- destructive migrations
- data writes ที่เกิดหลัง deploy
- attachment writes ที่เกิดหลัง deploy

## 3) Rollback Triggers
ควรพิจารณา rollback ถ้าเจอ:
- login/use core flow ไม่ได้
- dashboard/checklist/incident critical path พัง
- migration error ที่กระทบ availability
- attachment or storage path error ที่ทำให้ workflow หลักล้ม
- queue/worker error ที่สะสมจนกระทบ core behavior

## 4) Immediate Containment
ก่อน rollback:

1. เปิด maintenance mode ถ้าความเสียหายกระทบ user หลายคน
2. หยุดการ deploy ต่อเนื่องทั้งหมด
3. ระบุ revision ล่าสุดที่ stable
4. ระบุว่ามี migration หรือ data mutation อะไรเกิดขึ้นหลัง release

## 5) Rollback Procedure
ลำดับแนะนำ:

1. checkout หรือ deploy กลับไป revision ล่าสุดที่ stable
2. รัน `composer install --no-dev --optimize-autoloader`
3. restore build artifact หรือรัน `npm ci && npm run build`
4. รัน `php artisan config:clear`
5. รัน `php artisan cache:clear`
6. rebuild caches ตาม baseline:
   - `php artisan config:cache`
   - `php artisan view:cache`
   - `php artisan route:cache` เฉพาะเมื่อปลอดภัย
7. restart queue worker / process manager
8. รัน smoke checklist ซ้ำ
9. ปิด maintenance mode เมื่อ flow หลักกลับมาปกติ

## 6) Migration Rollback Stance
ห้าม assume ว่า `php artisan migrate:rollback` เป็น default rollback strategy

ต้องใช้ operator judgment เสมอ เพราะ:
- migration บางตัวอาจมี data loss risk
- migration rollback path อาจไม่ครอบคลุม production writes ที่เกิดไปแล้ว

ถ้า release นั้นแตะ schema:
- ต้องประเมินก่อนว่า rollback แบบ code-only เพียงพอหรือไม่
- ถ้าไม่พอ ต้องถือเป็น incident ที่ต้องมี DB recovery stance จาก phase ถัดไป

## 7) Post-Rollback Requirements
หลัง rollback ต้องบันทึก:
- release ที่ล้มคือ revision ไหน
- stable revision ที่กลับไปคือ revision ไหน
- trigger อะไรทำให้ rollback
- user impact เป็นอย่างไร
- ต้องแก้อะไรก่อนลอง release ใหม่

## 8) Known Limitation
หากยังไม่มี backup/recovery proof:
- rollback discipline นี้ยังไม่สมบูรณ์
- เป็นเพียง `application release rollback baseline`
- ยังไม่ใช่ full recovery capability
