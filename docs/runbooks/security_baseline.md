# Security Baseline
วันที่: 23 เมษายน 2026

## 1) Scope
baseline นี้ใช้กับระบบปัจจุบันในฐานะ:
- internal-only web app
- single organization
- 3 roles only
- room-centered lab operations
- lightweight attachment support

## 2) Current Security Controls Already Present
- session-based auth ผ่าน Laravel/Fortify
- login rate limit `5/minute` ต่อ email+IP
- two-factor authentication feature เปิดใน Fortify
- password reset flow มี
- active account gate มี
- route-level role gate มี
- passwords ถูก hash และ validate ตาม Laravel password rules

## 3) Key Security Concerns For This Product
- unauthorized access to internal operations data
- privilege misuse by authenticated users
- weak handling of admin credentials
- attachment abuse or unsafe file upload
- local/production secret leakage
- production misconfiguration เช่น `APP_DEBUG=true`

## 4) Attachment Risk Stance
repo truth ปัจจุบัน:
- attachment เป็น optional file upload
- validation ตอนนี้คือ `nullable|file|max:10240`
- storage ปัจจุบันผูกกับ `public` disk

security baseline ที่ต้องพูดตรง:
- phase นี้ยังไม่ได้ปิดเรื่อง MIME restrictions แบบ production-grade
- phase นี้ยังไม่ได้ปิด malware scanning
- phase นี้ยังไม่ได้ปิด retention/privacy enforcement

ดังนั้น attachment support ปัจจุบันคือ `useful but not yet deeply hardened`

## 5) Admin Hardening Minimum Rules
- admin accounts ต้องมี owner ชัดเจน
- admin account handoff ต้องผ่าน internal channel ที่ไว้ใจได้
- ห้ามแชร์ password ผ่านช่องทางไม่ปลอดภัย
- ห้ามลดสิทธิ์หรือปิดบัญชี admin หลักแบบไม่มี owner backup
- ถ้ามีการเปลี่ยน password ของผู้ใช้อื่น ต้องบันทึก responsibility ให้ชัด

## 6) Session and Authentication Minimum Rules
- production ต้องใช้ HTTPS
- session cookie ต้อง secure ใน production
- debug ต้องปิด
- login failures ที่ผิดปกติควรถูก review ตาม observability baseline
- two-factor ควรถูกเปิดใช้งานจริงสำหรับ admin อย่างน้อย

## 7) Authorization Stance
- coarse route-level role gates ใช้ได้สำหรับ baseline ปัจจุบัน
- object-level authorization ที่ลึกกว่านี้ยังต้องประเมินเพิ่มถ้าระบบขยาย scope
- ห้ามอ้าง fine-grained authorization maturity เกินของจริง

## 8) Honest Limitation
baseline นี้ยังไม่เท่ากับ security hardening complete

สิ่งที่ยังไม่มีหลักฐาน:
- formal pen test
- attachment scanning
- comprehensive abuse-case coverage
- security event monitoring integration

ดังนั้น phase นี้เป็น `security baseline documentation`, ไม่ใช่ security closure
