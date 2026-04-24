# Production Environment Contract
วันที่: 23 เมษายน 2026

## 1) Scope
เอกสารนี้ล็อก `production v1 operating contract` สำหรับระบบปัจจุบัน
โดยยึด repo truth ณ ตอนนี้:
- room-centered internal lab operations
- single organization
- internal provisioning only
- attachment support ผ่าน secure download route โดยเก็บไฟล์บน host-local private storage
- no machine registry
- not HA / not multi-region / not multi-tenant

## 2) Supported Production Baseline
production v1 รองรับ baseline นี้เท่านั้น:

- PHP 8.4
- Laravel 13 app current branch
- MySQL 8.0 database
- single-node web app host
- host-local private attachment storage on same host
- `database` queue
- `database` cache
- `database` session
- daily file logs

สิ่งที่ยังไม่อยู่ใน baseline:
- PostgreSQL official support claim
- Redis-required contract
- S3/object storage-required contract
- multi-node deployment
- autoscaling

## 3) Production-Safe Defaults
ค่าต่อไปนี้ต้องไม่คงค่าแบบ local เมื่อ deploy production:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` ต้องเป็น production URL จริง
- `LOG_LEVEL` อย่างน้อย `info`
- `DB_CONNECTION=mysql`
- `MAIL_MAILER=smtp`
- `QUEUE_CONNECTION=database`
- `CACHE_STORE=database`
- `SESSION_DRIVER=database`
- `SESSION_SECURE_COOKIE=true`
- `LOG_CHANNEL=daily` หรือ `LOG_CHANNEL=stack` ที่ include `daily`
- `LOG_LEVEL=info` หรือเข้มกว่านั้น

## 4) Secrets Handling Minimum Policy
- ห้าม commit secrets ลง repo
- ห้ามใช้ `.env` จาก local เป็น source สำหรับ production
- production secrets ต้องถูก inject ที่ host/deployment environment เท่านั้น
- `APP_KEY`, DB credentials, SMTP credentials, และ cloud credentials ต้องมี owner ชัดเจน
- ถ้ายังไม่มีวิธีหมุน secrets อย่างน้อยในเชิง procedure ห้ามอ้างว่า environment hardening ปิดแล้ว

## 5) Storage Contract
production v1 ใช้ host-local private storage baseline ตามระบบปัจจุบัน

ความหมาย:
- attachment files อยู่บน host เดียวกันกับแอปใน private storage path
- ต้องมี writable storage path
- attachment access ต้องผ่าน authenticated route เท่านั้น
- phase นี้ยังไม่อ้าง durability class แบบ object storage

ข้อจำกัดที่ต้องพูดตรง:
- ถ้า host เดียวหายและไม่มี backup/restore phase ตามมา ไฟล์แนบก็ยังเสี่ยง
- ดังนั้น phase นี้ยังไม่ใช่ backup/recovery closure

## 6) Queue / Cache / Session Contract
production v1 ล็อกเป็น:
- queue = `database`
- cache = `database`
- session = `database`

เหตุผล:
- ใกล้ repo truth ปัจจุบัน
- ลด infra expansion ก่อน phase deploy/ops พร้อม
- เหมาะกับ single-node baseline

ข้อจำกัด:
- ไม่ควรอ้างว่ารองรับงาน async หนัก
- ไม่ควรอ้างว่ารองรับ high-throughput concurrency โดยไม่มี measurement
- ถ้าจะเปลี่ยนเป็น Redis ภายหลัง ให้ถือเป็น phase ใหม่

## 7) Production Env Checklist
ก่อนจะเรียกว่า production environment contract พร้อมใช้ ต้องตอบคำถามนี้ได้ครบ:

- production ใช้ database อะไร
- production ใช้ storage อะไร
- production ใช้ queue/cache/session อะไร
- production secrets ถูกจัดการอย่างไร
- production mail ส่งออกทางไหน
- production debug/log level เป็นอะไร
- ใครเป็น owner ของ env file/secrets/worker/cron
- ใครตรวจ production contract violations ก่อนเปิดใช้ app จริง

## 8) Gate
ห้าม deploy production ถ้ายังไม่มี:
- env values ตาม contract นี้
- production DB ที่ตรง baseline
- queue/cache/session tables ครบ
- writable attachment storage
- SMTP config จริง
- secure session cookies เปิดจริง
- logging baseline เป็น daily files และไม่ใช้ debug level

## 9) Follow-On Phase Dependency
หลังเอกสารนี้ ต้องไปต่อที่:
- deployment and rollback discipline
- backup and recovery
- logging and observability
- security baseline

เอกสารนี้อย่างเดียวไม่เพียงพอที่จะ claim ว่า production-ready
