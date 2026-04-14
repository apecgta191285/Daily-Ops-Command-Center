# **F2 Incident Triage Execution Pack**

## *Execution Pack for Incident Workflow Improvement*

**DOC-33-F2-EP | แผนลงมือทำ Phase F2 แบบ task-by-task**  
**Version v1.0 | Execution reference | วันที่อ้างอิง 14/04/2569**

วัตถุประสงค์: เอกสารฉบับนี้แตก Phase F2 จากเอกสาร 31 ให้เป็นลำดับงานพร้อมลงมือจริง โดยโฟกัสการยกระดับ incident module จาก “รายการข้อมูล” ไปสู่ “รายการงานที่ติดตามต่อได้”

---

# **1. Phase Goal**

ทำให้ supervisor/admin ใช้ incident module แล้วสามารถ:

* มองเห็นว่าเรื่องไหนค้างนาน
* เห็นบริบทการติดตามงานชัดขึ้น
* บันทึก next step สั้น ๆ ได้เมื่อต้องอัปเดตสถานะ

---

# **2. Scope for This Slice**

## **Included**

* stale / aging visibility ใน incident detail
* timeline clarity ที่อ่านง่ายขึ้น
* optional next action note ระหว่างเปลี่ยน status
* regression coverage สำหรับ note + age visibility

## **Excluded**

* assignment
* due date engine
* SLA logic
* threaded comments
* notifications

---

# **3. Execution Decision**

สำหรับ F2 รอบแรก ให้ใช้กติกา:

* stale incident = unresolved incident ที่อายุอย่างน้อย 2 วัน
* next action note เป็น optional free-text note ที่ถูก append ลง activity trail เมื่อ status เปลี่ยน
* ถ้า status ไม่เปลี่ยน จะไม่สร้าง note activity แยกในรอบนี้

เหตุผล: ได้ triage value เพิ่มจริง โดยไม่สร้าง workflow complexity เกิน A-lite

---

# **4. Task Order**

## **F2-T1 Add optional next-action note to status transition**

### **Output**

* action layer รับ optional note
* status change สร้าง activity เพิ่มเมื่อมี note

## **F2-T2 Improve incident detail visibility**

### **Output**

* age/open-for display
* stale badge ในหน้ารายละเอียด
* timeline แยกให้เห็น status change กับ next action note ชัดขึ้น

## **F2-T3 Add regression coverage**

### **Output**

* tests สำหรับ action layer
* tests สำหรับ Livewire status update with note
* tests สำหรับ stale visibility/detail rendering

---

# **5. Acceptance Criteria**

* management สามารถใส่ next action note ได้ตอนเปลี่ยน status
* note ถูกบันทึกใน activity trail อย่าง append-only
* incident detail แสดง age/stale state ได้ถูกต้อง
* no-op status update ยังไม่สร้าง activity ใหม่
* php tests และ browser smoke baseline ยังผ่าน
