# **R5 Checklist Incident Prefill Extraction Execution Pack**

## *DOC-48-R5 | Extract checklist-to-incident prefill contract into application-owned support*

**Version v1.0 | Execution reference | วันที่อ้างอิง 17/04/2569**

วัตถุประสงค์: เอกสารฉบับนี้บันทึกการแยก logic การส่งต่อ context จาก daily checklist ไปยัง incident create flow ออกจาก Livewire component เพื่อให้ contract ของ handoff มี owner เดียว, ขยายต่อได้ง่าย, และไม่ทำให้ checklist surface โตแบบผิดชั้นเมื่อ product wave ถัดไปเพิ่ม anomaly memory หรือ richer follow-up context

---

# **1. Why This Slice Now**

จาก audit หลัง `N1-N4` และ `R1-R2` พบว่า [DailyRun.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Livewire/Staff/Checklists/DailyRun.php) ยังสร้าง query-string prefill ของ incident เองโดยตรง แม้ปัจจุบัน logic ยังเล็ก แต่ถ้าปล่อยให้โตต่อพร้อม feature wave อย่าง anomaly memory หรือ stronger follow-up prompts จะทำให้ component รับผิดชอบทั้ง workflow state และ transport-shaping พร้อมกัน

ดังนั้น R5 ถูกเลือกเป็น refactor ที่คุ้มค่าเพราะ:

* ลด coupling ระหว่าง checklist UI กับ incident create UI
* สร้าง contract กลางสำหรับ future checklist follow-up context
* กันไม่ให้ query-shaping logic กระจายซ้ำสองฝั่ง

---

# **2. Scope**

ในรอบนี้ทำเฉพาะ:

* เพิ่ม `ChecklistIncidentPrefill` data contract
* เพิ่ม `ChecklistIncidentPrefillBuilder` ฝั่ง checklist
* เปลี่ยน `DailyRun` ให้ใช้ builder แทน string assembly โดยตรง
* เปลี่ยน `Incident Create` ให้ parse request ผ่าน prefill contract เดียวกัน
* เพิ่ม unit coverage สำหรับ builder/contract
* อัปเดต current-state และ README ให้ตรงกับ implementation

สิ่งที่ยังไม่ทำ:

* anomaly memory
* dynamic severity escalation จาก history
* incident prefill rules based on repeated checklist failures

---

# **3. Implementation Notes**

## **3.1 New ownership**

owner ใหม่ของ checklist follow-up handoff:

* [ChecklistIncidentPrefill.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Checklists/Data/ChecklistIncidentPrefill.php)
* [ChecklistIncidentPrefillBuilder.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Checklists/Support/ChecklistIncidentPrefillBuilder.php)

## **3.2 Behavioral contract**

contract ที่ล็อกในรอบนี้:

* checklist follow-up ใช้ `from=checklist` เพื่อระบุ context
* title เริ่มต้นคือ `Checklist follow-up issue`
* category เริ่มต้นคือ `Other`
* severity เริ่มต้นคือ `Medium` เมื่อมี `Not Done`, ไม่เช่นนั้นเป็น `Low`
* description ประกอบจาก:
  * static follow-up line
  * template title
  * run date
  * not-done item titles

## **3.3 Parsing contract**

incident create page จะ restore prefill เฉพาะเมื่อ:

* `from=checklist`
* category และ severity ยังอยู่ใน canonical allow-list

ค่าที่ไม่ valid จะไม่ถูก hydrate กลับเข้าฟอร์ม

---

# **4. Verification**

ก้อนนี้ถือว่าผ่านเมื่อ:

* builder unit test ผ่าน
* prefill contract parse test ผ่าน
* checklist feature tests เดิมยังผ่าน
* incident create prefill tests เดิมยังผ่าน
* full regression baseline ยังเขียว

---

# **5. Outcome**

ผลลัพธ์ของ R5:

* checklist surface บางลงอีกขั้น
* checklist-to-incident handoff มี owner เดียวและขยายได้
* future wave เช่น anomaly memory หรือ richer follow-up context สามารถต่อยอดได้โดยไม่ย้อนกลับไปฝัง logic ใน Livewire component
