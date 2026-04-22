# Recovery Readiness Checklist
วันที่: 23 เมษายน 2026

## Backup Readiness
- มี backup policy เป็นลายลักษณ์อักษร
- มี owner ของ backup ชัดเจน
- รู้ที่เก็บ backup artifacts
- backup database ทำได้ตาม cadence ขั้นต่ำ
- backup attachments ทำได้ตาม cadence ขั้นต่ำ

## Restore Readiness
- มี restore runbook
- มี owner ของ restore ชัดเจน
- มี maintenance decision path ระหว่าง restore
- รู้ว่าจะ validate อะไรหลัง restore
- มี template สำหรับบันทึก restore drill/result

## Evidence Readiness
- มีอย่างน้อย 1 restore drill planned
- ถ้ามี drill แล้ว ต้องมี evidence note
- ถ้ายังไม่มี drill ต้องติดป้ายชัดว่า `policy-ready but not yet proven`

## Go / No-Go Interpretation
- ถ้าขาด backup policy = `NO-GO`
- ถ้าขาด restore runbook = `NO-GO`
- ถ้าขาด backup owner = `NO-GO`
- ถ้ามี policy/runbook ครบ แต่ยังไม่มี drill = `PARTIAL`
- ถ้ามี policy/runbook + drill evidence = `READY FOR NEXT HARDENING STEP`
