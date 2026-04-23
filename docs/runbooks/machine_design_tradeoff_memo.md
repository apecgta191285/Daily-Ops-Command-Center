# Machine Design Trade-Off Memo
วันที่: 23 เมษายน 2026

## 1) Purpose
memo นี้ใช้สรุป trade-offs เชิงวิศวกรรมของการเปิด machine domain หลัง Option A
โดยตั้งใจคัด model ที่ `coherent` ที่สุดไปสู่ Phase 4.3

## 2) Decision Frame
คำถามจริงของ phase นี้ไม่ใช่:
- “เราสร้าง machine table ได้ไหม”

แต่คือ:
- “ถ้าจะเปิด machine domain เราควรเปิดแบบไหนจึงไม่ทำลาย product truth เดิม”

## 3) Trade-Off Summary
### ถ้าเลือก free-text only ต่อไป
จะได้:
- ความง่าย
- ต้นทุนต่ำ

แต่จะเสีย:
- canonical asset identity
- reliable machine history
- machine-level analytics potential

### ถ้าเลือก machine-first everywhere
จะได้:
- ความเป็นระบบสูงสุดในเชิง asset identity

แต่จะเสีย:
- domain fit กับ non-machine issues
- simplicity ของ current workflow
- flexibility ของ incident domain

### ถ้าเลือก room-first with optional machine identity
จะได้:
- ความต่อเนื่องกับ current system
- ทางไป machine history
- การรองรับ mixed issue domain อย่างสมเหตุสมผล

แต่ต้องยอมรับ:
- schema และ UX จะซับซ้อนขึ้น
- governance rules จะต้องชัด
- duplication risk ระหว่าง machine label กับ free text ต้องถูกจัดการ

## 4) Recommended Architectural Stance
phase นี้แนะนำ stance แบบนี้:

1. `room` ต้องยังเป็น top-level operational context
2. `machine` ควรเป็น optional first-class entity
3. `incident.machine_id` ถ้ามีในอนาคต ควรเป็น nullable
4. `equipment_reference` ควรยังอยู่ต่อในช่วงแรก
5. machine lifecycle ไม่ควรถูกออกแบบเกินจำเป็นใน wave แรก

## 5) Why This Stance Is Senior-Engineer Correct
เพราะมัน:
- เคารพ existing truth ก่อน future ambition
- ไม่ optimize เร็วเกิน requirement
- ลดโอกาสสร้าง brittle domain model
- ป้องกัน “architecture theater” ที่ดูดีแต่ใช้จริงลำบาก
- เปิด space ให้ Phase 4.3 ประเมิน cost/value ได้อย่างซื่อสัตย์

## 6) Risks To Watch In Phase 4.3
ถ้าจะพา candidate นี้ไปต่อ ต้องประเมินเพิ่มเรื่อง:
- schema churn
- migration complexity
- seed/demo rewrite cost
- UI form burden
- test surface expansion
- docs and training overhead
- data ambiguity ระหว่าง machine label และ free text reference

## 7) Current Recommendation
คำแนะนำของ memo นี้คือ:

- `GO to Phase 4.3 evaluation`
- แต่ยัง `NO-GO for implementation`

เพราะเรามี candidate model ที่ coherent แล้ว
แต่ยังไม่มีหลักฐานว่า cost/complexity คุ้มพอสำหรับ implementation wave

## 8) Final Brutal Truth
ทางเลือกที่ดู “เร็ว” ที่สุดไม่ใช่ทางเลือกที่ถูกที่สุดในระยะยาวเสมอ

การยัด machine domain แบบครึ่ง ๆ กลาง ๆ
อาจทำให้เราเสียทั้ง simplicity ของ Option A
และยังไม่ได้ machine truth ที่น่าเชื่อถือจริง

ดังนั้น ณ จุดนี้ ทางที่ถูกต้องคือ:
- เก็บ room-first baseline ไว้
- พา optional machine identity ไปประเมิน cost อย่างจริงจัง
- ยังไม่แตะ implementation ก่อนมี ROI decision
