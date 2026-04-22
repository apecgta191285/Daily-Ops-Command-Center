# Observability Baseline
วันที่: 23 เมษายน 2026

## 1) Scope
baseline นี้ใช้กับ `production v1 single-node baseline`
และตั้งใจตอบคำถามขั้นต่ำว่า:
- จะดูอาการผิดปกติจากอะไร
- จะรู้ได้อย่างไรว่าระบบกำลังมีปัญหา
- จะ triage อย่างไรเมื่อมี failure

## 2) Current Truth
ตอนนี้ระบบมี:
- Laravel logging baseline
- app-level failures ที่ surface ผ่าน exception/error pages และ logs
- product-level workboard/dashboard signals สำหรับผู้ใช้
- browser/feature tests สำหรับ pre-release confidence

ตอนนี้ระบบยังไม่มี:
- external error aggregation ที่ลงจริง
- centralized metrics dashboard
- distributed tracing
- automatic uptime/latency alerting ที่พิสูจน์แล้ว

## 3) Minimum Production Logging Policy
production v1 ควรใช้ baseline นี้:
- log channel หลัก = `daily`
- log level ขั้นต่ำ = `info`
- critical failures ต้องเข้า channel ที่ review ได้จริง
- deprecation logs ต้องไม่ปนกับ production incident triage โดยไม่มี owner

## 4) What To Log
ควรมีอย่างน้อย:
- unhandled exceptions
- failed jobs / queue failures
- deployment/release notes ที่อ้างอิงได้
- restore/rollback events
- severe attachment/storage failures
- authentication or authorization failures ที่มีนัย operational

## 5) What Not To Log
ห้าม log:
- raw passwords
- secrets
- full sensitive tokens
- file contents
- noisy debug traces ใน production แบบไม่มีเหตุผล

## 6) Production v1 Observability Stance
สำหรับ v1:
- local logs + daily rotation เป็น baseline
- operator review cadence ต้องมี
- external monitoring choice ยังเปิดได้ แต่ phase นี้ยังไม่ถือว่า landed

## 7) Operational Review Cadence
ขั้นต่ำควรมี:
- review หลัง deploy ทุกครั้ง
- review เมื่อมี user-facing failure
- periodic log review อย่างน้อยรายสัปดาห์ในช่วง early production

## 8) Honest Limitation
baseline นี้ยังเป็น `policy and stance`
ไม่ใช่ observability platform closure

จนกว่าจะมี:
- monitoring integration จริง
- alert routing จริง
- owner response evidence

ยังไม่ควร claim ว่า observability hardening ปิดแล้ว
