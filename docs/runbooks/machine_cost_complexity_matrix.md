# Machine Cost / Complexity Matrix
วันที่: 23 เมษายน 2026

## 1) Purpose
matrix นี้ใช้ประเมินต้นทุนและความซับซ้อนของการเปิด `Option B`
โดยยึด candidate model แบบ `room-first with optional machine identity`

## 2) Cost / Complexity Matrix
| Impact area | Expected cost | Complexity | Why |
| --- | --- | --- | --- |
| Schema and migrations | Medium to High | High | ต้องเพิ่ม machine entity, relationships, nullable `machine_id`, และกติกาความสอดคล้องกับ room |
| Domain and application logic | Medium to High | High | create/update/query flows ต้องรองรับทั้ง machine-linked และ non-machine incidents |
| Incident create/edit UX | Medium | High | ต้องกำหนดว่าจะเลือก machine เมื่อไร, fallback เป็น free text เมื่อไร, และกันข้อมูลซ้ำซ้อนอย่างไร |
| Dashboard / queue / history surfaces | Medium | Medium to High | ต้องตัดสินใจว่าจะแสดง machine context อย่างไรโดยไม่ทำลาย room-first readability |
| Seed/demo data rewrite | Medium | Medium | demo story ปัจจุบันใช้ room + free-text framing อยู่ ต้องทบทวนหลายจุด |
| Test expansion | High | High | unit, feature, browser, and seeded scenario tests จะโดนผลกระทบกว้าง |
| Documentation and runbooks | High | Medium | current docs หลายฉบับล็อกไว้ชัดว่ายังไม่ใช่ machine registry ต้อง rewrite แบบเป็นระบบ |
| Training and user burden | Medium | Medium to High | staff/supervisor/admin ต้องเรียนกติกาใหม่เรื่อง machine vs non-machine issues |
| Long-term maintenance | Medium | High | ต้องดูแล machine lifecycle, data quality, and governance ต่อเนื่อง |

## 3) Where The Hidden Cost Really Is
ต้นทุนแฝงที่ phase นี้เห็นชัด:
- ไม่ได้อยู่แค่ “เพิ่ม table”
- แต่อยู่ที่การรักษา narrative และ domain truth ทั้งระบบไม่ให้แตก
- ถ้า machine label กับ `equipment_reference` ใช้ปนกันมั่ว จะได้ข้อมูลที่ดู structured แต่เชื่อถือไม่ได้

## 4) Value Side
value ที่เป็นไปได้ถ้าลง Option B จริง:
- canonical asset identity
- machine issue history
- repeated-failure insight
- future room inventory governance

แต่ value นี้จะเกิดจริงก็ต่อเมื่อ:
- ทีมมีวินัยกรอกข้อมูล machine อย่างสม่ำเสมอ
- machine registry ถูกดูแลจริง
- non-machine issues ยังถูกเก็บได้อย่างไม่บิดเบือน

## 5) Complexity Conclusion
ข้อสรุปของ matrix นี้คือ:
- Option B ไม่ใช่ “small extension”
- มันคือ `new capability layer`
- และมีผลกระทบข้าม schema, workflow, QA, docs, และ training พร้อมกัน

ดังนั้น implementation ไม่ควรเริ่มเพียงเพราะ model ดูน่าสนใจ
