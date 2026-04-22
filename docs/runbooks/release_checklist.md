# Release Checklist
วันที่: 23 เมษายน 2026

## Pre-Release Checks
- ยืนยัน release owner
- ยืนยัน target revision/commit
- ยืนยันว่าผ่าน `composer lint:check`
- ยืนยันว่าผ่าน `php artisan test`
- ยืนยันว่าผ่าน `npm run build`
- ยืนยัน browser smoke ที่จำเป็นสำหรับ release นี้
- ยืนยัน env contract ของ target host
- ยืนยันว่า release นี้มี migration หรือไม่
- ถ้ามี migration ให้ review risk ก่อน deploy

## Deployment Readiness
- host มี writable storage path
- `public/storage` พร้อมใช้งาน
- queue/cache/session tables ครบ
- SMTP config production พร้อม
- release window ถูกสื่อสารแล้ว
- rollback target revision ถูกระบุแล้ว

## During Deploy
- ประเมินว่าต้องเปิด maintenance mode หรือไม่
- deploy code revision ที่ถูกต้อง
- install PHP dependencies แบบ production
- build frontend assets
- clear and rebuild app caches ตาม runbook
- run migrations แบบ reviewed step เท่านั้น
- restart queue/process manager

## Post-Deploy Validation
- login ใช้งานได้
- dashboard เปิดได้
- staff checklist flow ใช้งานได้
- incident create เปิดได้
- management queue/detail/history ใช้งานได้
- attachment link/path ไม่พัง
- admin template/user governance surfaces เปิดได้

## Release Decision
- `GO`
- `GO WITH KNOWN LIMITATION`
- `ROLLBACK`

ต้องมี written note สั้นๆ ทุกครั้ง
