# Production Stack Decision Record
วันที่: 23 เมษายน 2026

## Decision
ล็อก `production v1` เป็น baseline นี้:

- database: `MySQL 8.0`
- storage: host-local `public` disk
- queue: `database`
- cache: `database`
- session: `database`
- deployment shape: `single-node`

## Context
repo ปัจจุบันพัฒนาบน local baseline แบบ:
- SQLite
- local/public disk
- database queue/cache/session

และ product scope ปัจจุบันยังเป็น:
- internal tool
- single organization
- room-centered lab operations
- lightweight attachment handling

## Why This Decision
1. ลดช่องว่างระหว่าง local truth กับ production baseline
2. ไม่บังคับ infra components ใหม่เกินจำเป็น
3. ใกล้เคียงกับ config/app behavior ปัจจุบันที่สุด
4. เหมาะกับ post-defense hardening order ที่ต้องเริ่มจาก environment contract ก่อน
5. ทำให้ phase ถัดไปอย่าง deploy/rollback/backup เขียนได้แบบ concrete

## Alternatives Considered
### 1. PostgreSQL baseline
ยังไม่เลือกตอนนี้
เพราะ phase นี้ยังไม่ได้ทำ cross-database verification แบบเต็มชุด และไม่ควร claim support เกินหลักฐาน

### 2. Redis + S3 baseline ตั้งแต่วันแรก
ยังไม่เลือกตอนนี้
เพราะจะเพิ่ม infra burden ก่อนมี deployment/ops ownership ชัด

### 3. Keep SQLite in production
ปฏิเสธ
เพราะขัดกับ production operating model ที่ควรมี DB service ชัดเจน และไม่เหมาะเป็น supported production baseline

## Known Limitations
- decision นี้ยังไม่เท่ากับ deploy-ready
- ยังไม่ปิดเรื่อง backup/restore
- ยังไม่ปิดเรื่อง observability
- ยังไม่ปิดเรื่อง security baseline
- ยังไม่ทำให้ระบบกลายเป็น production-grade ทันที

## Revisit Conditions
ให้ revisit decision นี้เมื่อ:
- phase deployment/rollback พร้อม
- มี measurement ว่า queue/cache needs โตเกิน database baseline
- มี requirement จริงสำหรับ object storage
- มีเหตุผลเชิงธุรกิจหรือ infra owner ที่ชัดให้รองรับ PostgreSQL หรือ Redis
