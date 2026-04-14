# **Feature Expansion Plan**

## *Execution-Ready Product Growth Plan After Solid Foundation*

**DOC-31-FEP | แผนพัฒนาฟีเจอร์ต่อยอดแบบพร้อมลงมือทำจริง**  
**Version v1.0 | Execution planning reference | วันที่อ้างอิง 14/04/2569**

วัตถุประสงค์: เอกสารฉบับนี้ใช้แตก roadmap เชิง product หลัง foundation เสร็จแล้วให้กลายเป็น phase การพัฒนาที่พร้อมลงมือทำจริง โดยคัดเฉพาะฟีเจอร์ที่คุ้มค่า, ทำจบได้, ไม่ over-engineer, และช่วยให้ระบบดู “มีของ” มากขึ้นอย่างชัดเจนในบริบทโครงงาน A-lite สำหรับ solo developer

---

# **1. Planning Principles**

## **1.1 สิ่งที่แผนนี้ยึด**

แผนนี้ยึดหลักดังนี้:

* ใช้ foundation ปัจจุบันเป็นฐาน ไม่ย้อนกลับไปรื้อ architecture ใหญ่
* เพิ่มเฉพาะฟีเจอร์ที่ส่งผลต่อ perceived value และ actual usability ชัดเจน
* ทุก phase ต้องมี output ที่ demo ได้จริง ไม่ใช่งาน refactor ที่ผู้ใช้ไม่เห็นผลเลย
* ทุก feature set ต้องเพิ่มภาระ maintenance ให้น้อยที่สุด
* งานที่เพิ่ม data contract ใหม่ต้องมี acceptance criteria และ regression coverage รองรับ

## **1.2 สิ่งที่แผนนี้ปฏิเสธ**

* feature spray แบบเพิ่มทุกอย่างพร้อมกัน
* enterprise features ที่เกิน A-lite
* over-abstracted architecture เพิ่มโดยไม่มี product pressure
* polish อย่างเดียวโดยไม่มี user value

---

# **2. Product Goal for the Next Wave**

## **2.1 เป้าหมาย**

เปลี่ยนระบบจาก:

**“web app ที่ flow หลักทำงานได้แล้ว”**

ไปสู่:

**“web app สำหรับทีมเล็กที่ดูช่วยงานได้จริง, ใช้ง่าย, สื่อคุณค่าได้ชัด, และเดโมแล้วดูจบ”**

## **2.2 Success Definition ของรอบนี้**

หลังจบแผนนี้ ระบบควรมีลักษณะดังนี้:

* management เปิด dashboard แล้วรู้ทันทีว่ามีอะไรต้องตาม
* staff ใช้ checklist แล้วรู้สึกว่าเป็น workflow ไม่ใช่แค่ฟอร์ม
* incident list/detail ช่วย triage งานได้จริงขึ้น
* หน้าแรกและ empty states สื่อคุณค่าของระบบได้
* demo scenario ชัดเจนและนำเสนอได้มั่นใจ

---

# **3. Priority Framework**

## **3.1 เกณฑ์ที่ใช้จัดลำดับ**

แต่ละ feature ถูกจัดลำดับจาก 4 มิติ:

* **User value:** ผู้ใช้ได้อะไรเพิ่มแบบชัดเจน
* **Demo value:** เวลาดูเดโมแล้วเห็นประโยชน์ทันทีหรือไม่
* **Engineering cost:** ใช้ effort เท่าไร
* **Foundation fit:** ใช้ฐานที่มีอยู่แล้วได้ดีแค่ไหน

## **3.2 Priority Buckets**

### **P0 — Must build next**

ฟีเจอร์ที่คุ้มที่สุดและควรทำก่อนเพื่อยกระดับ product จริง

### **P1 — Strong follow-up**

ฟีเจอร์ที่ดีและควรทำต่อหลัง P0 เสร็จ

### **P2 — Nice-to-have but deferable**

ฟีเจอร์ที่มีเหตุผล แต่ควรรอหลัง product core ดูจบแล้ว

---

# **4. Recommended Phase Order**

## **Phase F1 — Management Visibility Upgrade**

### **Objective**

ทำให้ dashboard มีคุณค่าจริงสำหรับ supervisor/admin

### **Why This Phase First**

เพราะ dashboard คือหน้าที่ “ขายระบบ” ให้คนดูเดโมและผู้ใช้ระดับ management ได้ดีที่สุด  
และใช้ข้อมูลที่ระบบมีอยู่แล้ว จึงให้ impact สูงในต้นทุนต่ำ

### **Scope**

* เพิ่ม section `Needs Attention Today`
* เพิ่ม alert cards สำหรับ:
  * checklist completion ต่ำกว่า threshold
  * unresolved high severity incidents
  * incidents ที่เปิดค้างนานเกิน threshold
* เพิ่ม quick links จาก dashboard ไปยัง incident list ที่ถูก filter แล้ว

### **User Value**

* supervisor รู้ว่าอะไรต้องตามทันที
* dashboard เปลี่ยนจากหน้า “สรุปตัวเลข” เป็นหน้า “ช่วยตัดสินใจ”

### **Technical Impact**

ต่ำถึงกลาง

### **Files/Areas Likely Touched**

* dashboard query/service
* dashboard controller/view
* incident list filter handling
* tests query + browser smoke

### **Acceptance Criteria**

* dashboard มี attention section ที่ render ได้ทั้งกรณีมีและไม่มี data
* quick links เปิดไปหน้ารายการ incident พร้อม filter ที่ถูกต้อง
* ไม่มี raw duplicated logic ฝังซ้ำในหลาย views
* tests ครอบคลุม card visibility และ filtered navigation

### **Risk Notes**

อย่าขยายไปถึง analytics หรือ charting หนักใน phase นี้

---

## **Phase F2 — Incident Triage Improvement**

### **Objective**

ทำให้ incident workflow ดูเหมือน “งานที่กำลังถูกติดตาม” มากขึ้น

### **Scope**

* เพิ่ม richer filters:
  * unresolved only
  * high severity only
  * date range
  * stale/open-too-long
* เพิ่ม aging/stale indicator ใน list และ detail
* ปรับ incident timeline ให้เห็นสถานะและกิจกรรมชัดขึ้น
* เพิ่ม optional `next action note` ตอน management เปลี่ยน status

### **User Value**

* supervisor จัดลำดับความสำคัญของปัญหาได้ง่ายขึ้น
* incident ไม่ดูเป็นแค่รายการข้อมูลนิ่ง

### **Technical Impact**

กลาง

### **Dependencies**

ควรทำหลัง F1 เพื่อให้ quick links จาก dashboard ไปยัง filters ใหม่ได้เลย

### **Acceptance Criteria**

* filter combinations ใช้งานได้จริงและไม่ทำ query พัง
* stale indicator มีเกณฑ์ชัดและประกาศใน code/docs
* next action note เก็บใน activity trail อย่างมีวินัย
* tests ครอบคลุม filtering + status update + activity rendering

### **Risk Notes**

อย่าขยายไปเป็น assignment system

---

## **Phase F3 — Checklist UX Upgrade**

### **Objective**

ทำให้ประสบการณ์ checklist ดูมีระบบและน่าใช้มากขึ้นสำหรับ staff

### **Scope**

* progress summary ระหว่างทำ
* section/group support สำหรับ checklist items
* stronger submit confirmation state
* lightweight staff history เช่น recent submissions ของตัวเอง

### **User Value**

* staff รู้ความคืบหน้าและไม่หลงในหน้าทำ checklist
* ระบบดูเหมือน workflow จริงมากขึ้น

### **Technical Impact**

กลาง

### **Dependencies**

ควรทำหลัง F1/F2 เพราะ F1/F2 เพิ่ม product value ที่เด่นต่อเดโมมากกว่า

### **Acceptance Criteria**

* checklist แสดง progress อย่างถูกต้องเมื่อกรอก result
* section/grouping ไม่ทำให้ daily run logic ซับซ้อนเกินจำเป็น
* recent submissions ของ staff ดึงจากข้อมูลจริง ไม่ใช่ fake content
* browser smoke ครอบคลุม happy path ใหม่

### **Risk Notes**

ยังไม่เพิ่ม draft workflow ใน phase นี้

---

## **Phase F4 — Product Framing and Demo Quality**

### **Objective**

ทำให้คนเปิดระบบครั้งแรกเข้าใจเร็วว่ามันช่วยอะไร และเดโมแล้วดูจบ

### **Scope**

* refresh หน้า landing ให้เล่า role-specific value ชัดขึ้น
* ปรับ empty states ของ dashboard/incidents/templates/checklist
* ทำ demo scenario seed set ที่สื่อ story ชัด
* เพิ่ม demo walkthrough notes สำหรับ role ต่าง ๆ

### **User Value**

* ผู้ประเมินและผู้ใช้เข้าใจระบบได้เร็วขึ้น
* perception ของความเป็น “โปรเจกต์จบที่พร้อมโชว์” ดีขึ้นมาก

### **Technical Impact**

ต่ำถึงกลาง

### **Acceptance Criteria**

* landing page บอก use case และ role value ชัด
* empty states ทุกหน้าหลักไม่แห้งและไม่โกหก feature
* demo seed data สามารถใช้กับ browser/manual walkthrough ได้จริง

### **Risk Notes**

อย่าทำ seed ให้กลายเป็น hard dependency ของ tests

---

## **Phase F5 — Selective Delivery Hardening**

### **Objective**

ปิดช่องว่างที่ทำให้ระบบดูยังไม่พร้อมใช้งานจริง แม้ feature หลักจะดีขึ้นแล้ว

### **Scope**

* เพิ่ม browser smoke coverage สำหรับ flows ใหม่ใน F1-F4
* เพิ่ม README/demo runbook สำหรับใช้งานจริงและการสอบ
* ทบทวน baseline deployment checklist แบบเบา
* เพิ่ม failure messaging ที่ user-facing สำหรับ edge cases สำคัญ

### **User Value**

* ทำให้ระบบ “ส่งมอบได้มั่นใจขึ้น”

### **Technical Impact**

ต่ำ

### **Acceptance Criteria**

* browser suite ครอบคลุม flow สำคัญเพิ่มขึ้น
* demo/run instructions ชัด
* known edge cases มีข้อความรองรับที่อ่านเข้าใจง่าย

---

# **5. Feature Backlog with Recommendation**

## **5.1 P0 Features**

### **P0-1 Dashboard Attention Panel**

**Recommendation:** ทำ  
**เหตุผล:** impact สูงสุดต่อ perceived usefulness

### **P0-2 Incident Aging and Stale Signals**

**Recommendation:** ทำ  
**เหตุผล:** ทำให้ incident module ดูเป็นเครื่องมือติดตามงานจริง

### **P0-3 Filtered Incident Drill-Down from Dashboard**

**Recommendation:** ทำ  
**เหตุผล:** เชื่อม dashboard กับ action ได้จริง

### **P0-4 Landing and Empty-State Product Framing**

**Recommendation:** ทำ  
**เหตุผล:** เพิ่ม demo value สูงมากโดยใช้ effort ต่ำ

## **5.2 P1 Features**

### **P1-1 Checklist Progress Summary**

**Recommendation:** ทำ  
**เหตุผล:** user-facing value สูงสำหรับ staff

### **P1-2 Checklist Section Grouping**

**Recommendation:** ทำแบบเบา  
**เหตุผล:** ดีต่อ usability แต่ต้องระวังไม่ทำ data model บวมเกินไป

### **P1-3 Incident Next Action Note**

**Recommendation:** ทำ  
**เหตุผล:** เพิ่ม sense ของ follow-up โดยไม่ถึง assignment system

### **P1-4 Staff Recent Submission Context**

**Recommendation:** ทำ  
**เหตุผล:** ช่วยให้ checklist experience ไม่รู้สึกจบแค่ submit แล้วหาย

## **5.3 P2 Features**

### **P2-1 Saved Filter Presets**

**Recommendation:** เลื่อน  
**เหตุผล:** ดีแต่ยังไม่จำเป็น

### **P2-2 Template Duplication**

**Recommendation:** พิจารณาหลัง P1  
**เหตุผล:** useful แต่ไม่ใช่ current pain สูงสุด

### **P2-3 Incident Export**

**Recommendation:** เลื่อน  
**เหตุผล:** low core value for current A-lite

---

# **6. Decisions for Scope Control**

## **6.1 ฟีเจอร์ที่ควรล็อกว่า “ยังไม่ทำ”**

เพื่อกันหลุด scope ควรล็อกไว้ก่อนว่า:

* ไม่มี assignment/reassignment
* ไม่มี notifications
* ไม่มี approvals
* ไม่มี multi-team branching
* ไม่มี advanced analytics dashboard
* ไม่มี API/microservice split

## **6.2 เหตุผล**

ถ้าระบบจะดู “โปรเจกต์จบที่ดี” ไม่จำเป็นต้องมีทุกอย่าง  
แต่ต้องมีสิ่งที่มีอยู่แล้ว “ทำงานได้ดีและสื่อคุณค่าได้ชัด”

---

# **7. Recommended First Execution Slice**

## **7.1 ชุดงานแรกที่ควรทำ**

ฉันแนะนำให้เริ่มด้วยชุดนี้ก่อน:

1. Dashboard attention panel  
2. Incident stale/high-severity indicators  
3. Dashboard to incident filtered drill-down  
4. Landing page framing refresh  
5. Key empty states refresh

## **7.2 ทำไมต้องเริ่มชุดนี้**

เพราะเป็นชุดที่:

* เปลี่ยน perception ของระบบเร็วที่สุด
* ใช้ฐานเดิมได้ดี
* ไม่ต้องแตะ schema ใหญ่
* เดโมแล้วเห็นผลชัดมาก

---

# **8. Engineering Guidance for the Next Wave**

## **8.1 Implementation Rules**

งาน feature รอบถัดไปควรยึดกติกาเหล่านี้:

* query logic อยู่ใน query/service layer ไม่ยัดใน Blade
* view components ใช้ซ้ำได้เมื่อมี presentation pattern ซ้ำ
* browser smoke เพิ่มตาม feature ที่มี user-facing impact สูง
* docs canonical อัปเดตเมื่อ contract เปลี่ยนจริงเท่านั้น
* ทุก feature ต้องมี “non-goal” ชัด เพื่อกันบานปลาย

## **8.2 Test Strategy**

* unit/query tests สำหรับ metrics และ filters
* feature tests สำหรับ authorization + route contract
* browser smoke เฉพาะ flow ที่เป็น product-critical

---

# **9. Final Verdict**

## **9.1 ข้อสรุป**

หลัง foundation เสร็จแล้ว สิ่งที่ควรทำต่ออย่างถูกต้องที่สุดคือ:

* เพิ่ม value ให้ dashboard
* เพิ่มความจริงจังให้ incident triage
* เพิ่มความลื่นและความชัดให้ checklist experience
* เพิ่ม framing ให้ระบบดูจบและเข้าใจง่าย

## **9.2 Brutal Truth**

ถ้าเราอยากให้ระบบดู “ดีกว่านี้มาก”  
เราไม่ต้องเพิ่มฟีเจอร์เป็นสิบ

เราแค่ต้องทำ 4-6 ฟีเจอร์ที่ตรงจุดจริงให้ดีพอ และเชื่อมกันเป็น story เดียว

นั่นคุ้มกว่าและดู professional กว่าการยัดระบบใหญ่แบบครึ่ง ๆ กลาง ๆ

## **9.3 Next Step**

ขั้นถัดไปที่ถูกต้องที่สุดคือแตก phase F1 ออกเป็น:

**Execution Pack: Dashboard and Triage Upgrade**

เพื่อเริ่มลงมือทำฟีเจอร์ชุดแรกอย่างเป็นระบบ
