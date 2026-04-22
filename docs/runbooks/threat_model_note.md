# Threat Model Note
วันที่: 23 เมษายน 2026

## 1) Protected Assets
สิ่งที่ต้องปกป้องอย่างน้อย:
- user accounts
- role assignments
- room-aware checklist and incident data
- attachment files
- admin template/user governance surfaces
- environment secrets

## 2) Threat Actors
### External attacker
พยายามเดา login หรือใช้ misconfiguration เพื่อเข้าระบบ

### Internal but unauthorized user
มีบัญชีอยู่แล้ว แต่พยายามเข้าหน้าที่เกิน role

### Privileged misuse
admin หรือ supervisor ใช้งานเกินขอบเขตหรือจัดการบัญชี/ข้อมูลโดยไม่เหมาะสม

### Operational misconfiguration
ระบบถูก deploy ด้วย debug on, weak secrets, หรือ storage/session config ไม่ปลอดภัย

## 3) High-Level Risks
- brute-force login attempts
- leaked credentials
- overexposed attachment links
- role boundary failure
- accidental admin misuse
- lost or leaked `.env` / secret material

## 4) Current Mitigations
- Fortify auth
- login throttling
- active-user enforcement
- role gate middleware
- password hashing
- internal provisioning only

## 5) Risks Still Open
- attachment content scanning ยังไม่มี
- attachment MIME allowlist ยังไม่เข้ม
- secret rotation procedure ยังไม่พิสูจน์
- admin operational misuse ยังพึ่ง SOP/policy มากกว่าระบบบังคับ
- monitoring for suspicious auth patterns ยังไม่ integrated

## 6) Honest Conclusion
threat model นี้พอสำหรับ `production baseline planning`
แต่ยังไม่ใช่ formal security review artifact ระดับองค์กร
