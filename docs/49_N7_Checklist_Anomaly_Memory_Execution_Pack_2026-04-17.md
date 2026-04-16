# **N7 Checklist Anomaly Memory Execution Pack**

## *DOC-49-N7 | Add lightweight recurring-issue memory to the daily checklist flow*

**Version v1.0 | Execution reference | วันที่อ้างอิง 17/04/2569**

วัตถุประสงค์: เพิ่ม lightweight memory ให้ checklist item ที่เพิ่งถูก mark `Not Done` ในประวัติ recent runs ของผู้ใช้ เพื่อให้ staff เห็น pattern ของปัญหาซ้ำได้เร็วขึ้น โดยยังคง scope แบบ A-lite และไม่เปิด analytics layer เต็มรูปแบบ

---

# **1. Why This Slice Now**

หลังจาก `F3` และ `R5` checklist flow มี progress, recap, grouping, และ follow-up handoff แล้ว แต่ยังขาดสิ่งที่ทำให้ flow ดู “ฉลาดขึ้น” ในระดับพอดี นั่นคือความจำของปัญหาซ้ำ

ก้อนนี้ถูกเลือกเพราะ:

* เพิ่ม user value ชัดโดยไม่แตะ schema
* ใช้ baseline จาก recent-run context ที่มีอยู่แล้ว
* ไม่บานไปเป็น analytics, heatmap, หรือ scoring system

---

# **2. Scope**

ในรอบนี้ทำเฉพาะ:

* builder กลางสำหรับ anomaly memory จาก recent submitted runs
* แสดง hint บน checklist item เมื่อ item เดิมเคยถูก mark `Not Done`
* แสดง recap emphasis เมื่อรอบปัจจุบัน mark `Not Done` ซ้ำกับประวัติล่าสุด
* เพิ่ม unit/feature coverage

นอก scope:

* anomaly scoring
* cross-user team memory
* trend chart ของ checklist item
* dynamic severity escalation อัตโนมัติ

---

# **3. Behavioral Contract**

contract ที่ล็อกในรอบนี้:

* พิจารณาเฉพาะ `submitted runs`
* จำกัด recent memory เฉพาะผู้ใช้เดียวกัน
* จำกัดใน template เดียวกัน
* ถ้ามี item เดียวกันถูก mark `Not Done` ใน recent runs ให้แสดง:
  * จำนวนครั้ง
  * sample run count
  * วันที่ล่าสุดที่เกิด
  * note ล่าสุด ถ้ามี

---

# **4. Intended UX Outcome**

ก่อนตอบ checklist:

* staff จะเห็นว่า item ไหนมีประวัติปัญหาซ้ำ
* ช่วยให้ไม่มอง checklist เป็นแค่ฟอร์มกรอก แต่เป็น workflow ที่มี memory

หลัง submit:

* ถ้ารอบนี้มี item ที่เคยเป็นปัญหาซ้ำมาก่อน จะมี repeated-issue reminder ใน recap
* ช่วย reinforce การส่งต่อไป incident flow เมื่อจำเป็น

---

# **5. Verification**

ก้อนนี้ถือว่าผ่านเมื่อ:

* unit test ของ anomaly memory builder ผ่าน
* feature test ของ checklist page เห็น memory hint
* feature test ของ submission recap เห็น repeated-issue emphasis
* full regression baseline ยังเขียว
