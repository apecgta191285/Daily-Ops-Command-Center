# Program 1 / Phase 1.5 — Security Baseline Execution Plan
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้แตก `Program 1 / Phase 1.5 — Security Baseline` ให้เป็นก้อนงานที่ใช้ได้จริง
โดยยังไม่อ้างว่าระบบผ่าน security hardening ครบทุกมิติ และยังไม่เปิด compliance/security tooling wave ใหม่

## 2) Repo Truth Used For This Plan
สิ่งที่ repo มีอยู่แล้ว:
- Fortify authentication
- login rate limit `5/minute`
- two-factor authentication feature เปิดอยู่
- password reset flow มีอยู่
- active-account enforcement มีอยู่
- route-level role gate มีอยู่
- password ถูก hash ผ่าน model cast/rules

สิ่งที่ repo ยังไม่มีเป็น baseline ที่เขียนชัด:
- threat model note
- secrets handling guide
- release security checklist
- attachment risk stance แบบเป็น policy
- admin hardening policy แบบเป็นลายลักษณ์อักษร

## 3) Executive Decision
phase นี้ควรจบด้วย baseline แบบนี้:
- security baseline doc
- threat model note
- secrets handling guide
- release security checklist
- explicit statement ว่าอะไรมีแล้วและอะไรยังไม่มี

แต่ยังไม่ claim ว่า:
- penetration tested แล้ว
- security review ปิดแล้ว
- compliance-ready แล้ว
- full audit trail / abuse detection พร้อมแล้ว

## 4) Deliverables
phase นี้ควรจบด้วย 4 deliverables:

1. [security_baseline.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/security_baseline.md)
2. [threat_model_note.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/threat_model_note.md)
3. [secrets_handling_guide.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/secrets_handling_guide.md)
4. [release_security_checklist.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/release_security_checklist.md)

## 5) Hard Boundaries
phase นี้ยังไม่ทำ:
- penetration test execution
- SAST/DAST integration
- WAF/CDN security program
- SIEM integration
- encryption key rotation program implementation
- enterprise IAM integration

## 6) Security Questions This Phase Must Answer
phase นี้ต้องตอบให้ได้:

1. attacker classes หลักของระบบนี้คือใคร
2. data/assets ที่ต้องป้องกันมีอะไร
3. auth/session/rate-limit baseline ตอนนี้มีอะไรจริง
4. attachment risk stance คืออะไร
5. admin account hardening ขั้นต่ำคืออะไร
6. secrets ต้องถูกจัดการอย่างไร

## 7) Acceptance Criteria
phase นี้จะถือว่าจบเมื่อ:
- repo มี security baseline ที่ grounded กับ current implementation
- มี threat model note ที่ไม่ generic เกินไป
- มี secrets handling guide
- มี release security checklist
- ไม่มีประโยคที่ overclaim ว่าระบบ production-secure แล้ว

## 8) Recommended Next Step After This Phase
เมื่อ phase นี้จบ `Program 1 — Platform Hardening Baseline` จะครบ baseline docs set
จากนั้นค่อยตัดสินว่าจะเริ่ม `Program 2 — Product Hardening` หรือจะลงมือพิสูจน์ runbooks บางส่วนก่อน
