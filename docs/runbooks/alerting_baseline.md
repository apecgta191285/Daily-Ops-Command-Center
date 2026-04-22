# Alerting Baseline
วันที่: 23 เมษายน 2026

## Goal
กำหนด baseline ว่าเหตุการณ์แบบไหนควรถูกมองว่า `ต้องรีบดู`
แม้ใน phase นี้จะยังไม่มี alert automation integration ก็ตาม

## Severity Buckets
### Critical
ตัวอย่าง:
- app เข้าไม่ได้
- login ใช้ไม่ได้
- dashboard/checklist/incident core path พัง
- DB connection error ต่อเนื่อง
- attachment/storage failure ที่ทำให้ workflow หลักใช้ไม่ได้

การตอบสนอง:
- triage ทันที
- พิจารณา maintenance mode หรือ rollback

### High
ตัวอย่าง:
- queue failure สะสม
- print/history/admin surfaces พังแต่ core path ยังใช้ได้
- repeated exception ใน production logs หลัง deploy

การตอบสนอง:
- review ภายในวันเดียวกัน
- ประเมิน hotfix หรือ rollback ตามผลกระทบ

### Medium
ตัวอย่าง:
- single surface warning
- deprecation notices ที่ไม่กระทบ user flow
- isolated attachment edge-case ที่ไม่ block core path

การตอบสนอง:
- ใส่เข้ารอบ review/hardening

## Alert Sources In v1
phase นี้ยอมรับ alert sources แบบ baseline:
- post-deploy smoke failure
- operator log review
- user-reported incident
- queue failure review

## Honest Limitation
เอกสารนี้ไม่ได้แปลว่ามี automated alerting แล้ว
มันเป็นเพียง severity and response baseline สำหรับ phase นี้
