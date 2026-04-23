# Option B GO / NO-GO Memo
วันที่: 23 เมษายน 2026

## 1) Decision
`NO-GO`

สำหรับ:
- immediate Option B implementation
- schema change wave
- machine registry coding phase

## 2) Reason
แม้ discovery จะยืนยันว่า machine registry มีคุณค่า `ในบาง use cases`
แต่ยังไม่มีหลักฐานพอว่าคุณค่านั้นคุ้มกับต้นทุนในตอนนี้

ประเด็นหลักคือ:
- current Option A baseline ยังตอบโจทย์ room-centered operations ได้จริง
- machine value อยู่ใน subset ของ incidents ไม่ใช่ทั้ง domain
- implementation จะกระทบ schema, UX, tests, docs, training, และ seeded story พร้อมกัน
- ถ้ายังไม่มี named owner / time / support ที่ชัด การเริ่ม Program 5 ตอนนี้เสี่ยงสูง

## 3) What Would Change This To GO Later
เงื่อนไขที่อาจทำให้ future decision เปลี่ยนเป็น `GO`:
- ทีมมี requirement จริงเรื่อง asset identity continuity
- มี recurring machine-level pain มากพอที่จะ justify complexity
- มี owner ชัดสำหรับ machine registry governance
- มีเวลาและแรงสำหรับ rewrite tests/docs/demo/training อย่างครบวงจร
- ยอมรับได้ว่ามันเป็น capability wave ใหม่ ไม่ใช่ patch เล็ก

## 4) What Must Not Happen Next
- ห้ามเริ่ม `machines` table แบบทดลองครึ่งทาง
- ห้ามยัด `machine_id` เข้า incident โดยยังไม่มี governance model
- ห้ามแก้ฟอร์ม create incident แบบ ad hoc เพื่อให้ดูเหมือนมี Option B
- ห้ามเอา discovery conclusion ไปตีความว่า implementation ได้รับอนุมัติแล้ว

## 5) Honest Recommendation
คำแนะนำที่ซื่อสัตย์ที่สุดตอนนี้คือ:

- ปิด Program 4 ในฐานะ discovery complete
- คง current Option A baseline ไว้
- เปิด Program 5 เฉพาะเมื่อมี resource, owner, และ business value ชัดจริง

## 6) Final Brutal Truth
การไม่ทำตอนนี้ ไม่ได้แปลว่า Option B ไม่ดี

มันแปลว่า:
- ตอนนี้เรารู้พอแล้วว่ามันคือ wave ใหม่ที่จริงจัง
- และการยังไม่เริ่ม คือการตัดสินใจเชิงวิศวกรรมที่รับผิดชอบกว่าการเริ่มแบบครึ่ง ๆ กลาง ๆ
