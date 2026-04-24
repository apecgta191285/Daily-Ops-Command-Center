# Environment Matrix
วันที่: 23 เมษายน 2026

## Purpose
เอกสารนี้ใช้ล็อกความต่างของ `local`, `staging`, และ `production`
เพื่อให้ทีมไม่ใช้ค่า local ไปอธิบาย production และไม่ deploy บนสมมติฐานที่ยังไม่ชัด

## Matrix
| Dimension | Local | Staging | Production |
|---|---|---|---|
| Purpose | dev / test / demo prep | pre-release verification | live internal use |
| App env | `local` | `staging` | `production` |
| Debug | `true` ได้ | `false` | `false` |
| Database | SQLite | MySQL 8.0 | MySQL 8.0 |
| Storage | local + `public` disk | host-local private attachment storage | host-local private attachment storage |
| Queue | `database` | `database` | `database` |
| Cache | `database` | `database` | `database` |
| Session | `database` | `database` | `database` |
| Mail | `log` / local mail catcher | SMTP test mailbox | SMTP production mailbox |
| Logging | local file-oriented | daily file logs | daily file logs + external review policy |
| Seed data | allowed | controlled only | not allowed |
| Browser QA | dev/test only | release-candidate smoke | post-deploy smoke only |

## Notes
- `production v1` ถูกล็อกเป็น `single-node baseline`
- phase นี้ยังไม่อ้าง Redis, S3, multi-node session sharing via external cache, หรือ HA architecture
- ถ้าจะเปลี่ยน database/storage/queue strategy ในอนาคต ต้องเปิด hardening phase ใหม่ ไม่ใช่แก้ env แบบ ad hoc

## Must-Not-Drift Rules
- ห้ามใช้ `APP_DEBUG=true` ใน staging หรือ production
- ห้ามใช้ SQLite เป็น production database
- ห้าม seed demo data เข้า production
- ห้ามถือว่า local attachment path = production deployment model โดยอัตโนมัติ หาก phase ถัดไปเปลี่ยน storage strategy

## Deployment Preconditions Implied By This Matrix
- staging และ production ต้องมี writable private storage path สำหรับ incident attachments
- staging และ production ต้องมี database tables สำหรับ queue, cache, และ sessions
- staging และ production ต้องมี cron/worker ownership ที่ชัดสำหรับ `database` queue
- staging และ production ต้องมี SMTP config ที่ไม่ใช้ `log` mailer
- staging และ production ต้องเปิด secure session cookies และใช้ daily file logs
