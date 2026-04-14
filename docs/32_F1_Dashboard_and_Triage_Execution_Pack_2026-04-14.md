# **F1 Dashboard and Triage Execution Pack**

## *Execution Pack for the First Product Expansion Slice*

**DOC-32-F1-EP | แผนลงมือทำ Phase F1 แบบ task-by-task**  
**Version v1.0 | Execution reference | วันที่อ้างอิง 14/04/2569**

วัตถุประสงค์: เอกสารฉบับนี้แตก Phase F1 จากเอกสาร 31 ให้เป็นลำดับงานที่พร้อมลงมือทำจริง โดยเน้นผลลัพธ์ที่เพิ่ม product value สูงสุดเร็วที่สุดและยังคงอยู่ในขอบเขต A-lite

---

# **1. Phase Goal**

ทำให้ management users เปิด dashboard แล้วรู้ทันทีว่า:

* วันนี้มีอะไรที่ควรสนใจ
* incident กลุ่มไหนมีความเสี่ยงหรือค้างนาน
* ต้องกดไปดูต่อที่หน้าไหน

---

# **2. Scope for This Slice**

## **Included**

* dashboard attention panel
* unresolved high-severity incident signal
* stale incident signal
* checklist completion attention state
* dashboard → incident list drill-down links
* incident list support สำหรับ query-driven initial filters

## **Excluded**

* charts
* notifications
* date-range reporting
* assignment
* next action note
* checklist UX changes

---

# **3. Task Order**

## **F1-T1 Expand dashboard query contract**

เพิ่มข้อมูล attention layer ใน dashboard query/service โดยไม่ย้าย logic ไปไว้ใน Blade

### **Output**

* high severity unresolved count
* stale unresolved count
* checklist attention state
* quick-link metadata สำหรับ incident drill-down

## **F1-T2 Render dashboard attention panel**

เพิ่ม section ในหน้า dashboard ให้มองเห็น risk/attention ได้ทันที

### **Output**

* attention cards
* empty state เมื่อไม่มีอะไรต้องตาม

## **F1-T3 Add incident drill-down support**

ทำให้หน้า incident list รับ initial filters จาก query string ได้ เพื่อรองรับ quick links จาก dashboard

### **Output**

* query params สำหรับ status / severity / unresolved / stale
* filter normalization ใน component

## **F1-T4 Add regression coverage**

เพิ่ม feature tests สำหรับ:

* dashboard attention rendering
* stale/high-severity counts
* dashboard quick-link paths
* incident list query-driven filtering

---

# **4. Acceptance Criteria**

* dashboard มี section `Needs Attention Today`
* ถ้าไม่มี risk/open issue ตามเกณฑ์ ระบบแสดง empty state ที่ชัด
* quick links จาก dashboard เปิดไปหน้า incident list พร้อม filter ที่ถูกต้อง
* incident list รับ filter จาก query string และไม่พังกับค่าที่ไม่รู้จัก
* php tests และ browser smoke baseline ยังผ่าน

---

# **5. Risk Control**

* ใช้ threshold แบบง่ายและ explicit ใน code
* ไม่สร้าง table/schema ใหม่
* ไม่เพิ่ม abstraction ใหม่เกินจำเป็น
* ถ้ามี query duplication ให้เก็บไว้ใน existing query/service layer เท่านั้น

---

# **6. Execution Decision**

สำหรับ F1 รอบแรก ให้ใช้ threshold ดังนี้:

* stale incident = unresolved incident ที่สร้างมาแล้วอย่างน้อย 2 วัน
* unresolved high severity = incident severity `High` และ status ไม่ใช่ `Resolved`
* checklist attention = today runs เป็น 0 หรือ completion rate ต่ำกว่า 100%

เหตุผล: threshold นี้เรียบง่าย, อธิบายได้ง่ายในการเดโม, และไม่ต้องสร้าง product policy ซับซ้อนเกิน A-lite
