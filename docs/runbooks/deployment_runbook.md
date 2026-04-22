# Deployment Runbook
วันที่: 23 เมษายน 2026

## 1) Scope
runbook นี้ใช้กับ `production v1 single-node baseline` เท่านั้น

มันไม่ได้ครอบคลุม:
- blue/green
- canary
- multi-node
- autoscaling
- zero-downtime guarantee

## 2) Preconditions
ก่อนเริ่ม deploy ต้องมี:
- approved release owner
- production env values ตาม [production_env_contract.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/production_env_contract.md)
- DB credentials ใช้งานได้
- writable `storage` paths
- `public/storage` symlink พร้อม
- queue/cache/session tables ครบ
- backup checkpoint หรืออย่างน้อย backup intent ที่ phase ถัดไปจะ formalize

## 3) Pre-Deploy Validation
บน release candidate หรือ pre-release surface:

1. `composer lint:check`
2. `php artisan test`
3. `npm run build`
4. browser smoke ที่คุ้มจริงสำหรับ current release
5. review migration diff ว่ามี destructive risk หรือไม่

ถ้าไม่ผ่านข้อใดข้อหนึ่ง ห้าม deploy

## 4) Deployment Procedure
ลำดับที่แนะนำ:

1. ประกาศ deployment window ให้ owner ที่เกี่ยวข้องทราบ
2. ถ้าจำเป็น เปิด maintenance mode
3. ดึง code revision ที่จะปล่อย
4. รัน `composer install --no-dev --optimize-autoloader`
5. รัน `npm ci`
6. รัน `npm run build`
7. ตรวจ `.env` production ให้ตรง contract
8. รัน `php artisan config:clear`
9. รัน `php artisan cache:clear`
10. รัน `php artisan migrate --force`
11. รัน `php artisan storage:link` เฉพาะเมื่อยังไม่มี symlink หรือ host ถูก reprovision
12. รัน `php artisan config:cache`
13. รัน `php artisan route:cache` เฉพาะเมื่อ release owner ยืนยันว่า route/cache contract ปลอดภัย
14. รัน `php artisan view:cache`
15. restart queue worker / process manager ตาม host policy
16. ปิด maintenance mode ถ้าเปิดไว้
17. ทำ post-deploy smoke ตาม [post_deploy_smoke_checklist.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/post_deploy_smoke_checklist.md)

## 5) Commands That Need Operator Judgment
คำสั่งต่อไปนี้ไม่ควรถูกมองว่า “กดได้เสมอ”:
- `php artisan migrate --force`
- `php artisan route:cache`
- maintenance mode open/close

เหตุผล:
- migration อาจมี data risk
- route cache ต้องระวังกับ package/runtime assumptions
- maintenance mode กระทบ user-facing availability

## 6) Release Owner Checklist
release owner ต้องตอบให้ได้:
- revision ไหนกำลัง deploy
- release นี้มี migration หรือไม่
- release นี้แตะ attachment/storage path หรือไม่
- release นี้มี worker behavior change หรือไม่
- smoke checks หลัง deploy คืออะไร
- rollback condition คืออะไร

## 7) Post-Deploy Decision
หลัง deploy ต้องสรุปเป็นหนึ่งใน 3 แบบ:
- `GO` = ใช้งานต่อได้
- `GO WITH KNOWN LIMITATION` = deploy ใช้ได้ แต่มีข้อสังเกตที่ไม่ block
- `ROLLBACK` = release นี้ไม่ควรคงอยู่

## 8) Notes
- runbook นี้ตั้งใจให้ pragmatic และสอดคล้องกับ current repo truth
- มันยังไม่ใช่ final operations excellence baseline
- phase ถัดไปต้องเสริม backup/recovery ก่อนจะอ้าง production discipline ที่แน่นขึ้น
