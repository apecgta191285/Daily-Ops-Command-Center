# **Post-QA Baseline Audit and Next Hardening Wave Plan**

**DOC-106 | Ground-truth audit after identity alignment, docs pruning, and QA tightening**  
**Date:** 2026-04-21  
**Status:** Analysis / planning baseline  
**Intent:** lock the next product wave without reopening feature creep

---

## **1. Executive Verdict**

### **Shortest brutal truth**

โปรเจ็คนี้ **มาถูกทางแล้วจริง** และ repository ตอนนี้ **ดีกว่า MVP เปล่าอย่างชัดเจน**

แต่คำตัดสินที่ยุติธรรมที่สุดยังเป็น:

**strong capstone / disciplined MVP+ / credible internal ops prototype**

ไม่ใช่:

* production-grade product
* enterprise platform
* fully hardened system

### **What changed for real**

จาก repo truth ปัจจุบัน:

* identity ถูกล็อกแล้วในฐานะ `University Computer Lab Daily Ops`
* docs entry layer ดีขึ้น
* story alignment ดีขึ้นบน entry surfaces และ major product surfaces หลายหน้า
* frontend governance ไม่ได้เป็นแค่แนวคิด แต่มี artifact และ QA proof จริง
* printable evidence convenience ยังอยู่ในกรอบ
* authenticated QA baseline ถูก tighten เพิ่ม และ accessibility debt บางจุดถูกปิดจริง

ดังนั้นปัญหาหลักตอนนี้ **ไม่ใช่ feature vacuum** และ **ไม่ใช่ foundation defect**

ปัญหาหลักคือ:

* type hardening ยังไม่ครบ
* query scalability hygiene ยังหยาบบางจุด
* documentation truth ดีขึ้นแล้ว แต่ยังไม่ lean พอในบางไฟล์
* story alignment ยังมี residual wording กระจายอยู่บางชั้น
* frontend governance มี baseline จริงแล้ว แต่ยังไม่ใช่ full-system gate

---

## **2. Ground Truth Confirmed From Code**

### **2.1 Identity lock is real**

สิ่งนี้เป็นความจริงแล้วจาก source ปัจจุบัน:

* `README.md`
* `docs/00_Project_Lock_v1.1.md`
* `docs/01_Product_Brief_v1.1.md`
* `docs/02_System_Spec_v0.3.md`
* `docs/04_Current_State_v1.3.md`
* `resources/views/welcome.blade.php`
* `resources/views/pages/auth/login.blade.php`

product identity ตอนนี้ถูกล็อกว่าเป็น:

**internal daily operations web app for university computer lab teams**

### **2.2 Route boundary is still healthy**

`routes/web.php` ยังแบ่ง route family ถูกต้อง:

* `staff`: checklist runtime + incident create
* `management`: dashboard + incidents + archive + print
* `admin`: templates + users + ui-governance

ตรงนี้สำคัญมาก เพราะหมายความว่า product โตขึ้น แต่ authorization shape ยังไม่เละ

### **2.3 Frontend QA baseline is no longer “not landed”**

ข้อความจาก audit ภายนอกที่บอกว่า frontend governance หรือ QA baseline “ยังไม่ลงจริง” ตอนนี้ **ไม่แม่นแล้ว**

ของที่มีจริงแล้วใน repo:

* browser smoke suite
* guest screenshot baseline
* deterministic authenticated admin governance screenshot baseline
* browser accessibility assertions
* UI governance guide

ข้อเท็จจริงที่แม่นที่สุดคือ:

**frontend QA baseline landed partially and meaningfully, but is not yet a full-system screenshot gate**

### **2.4 Accessibility automation exists in practice**

แม้ `package.json` จะไม่ได้มี `@axe-core/playwright` เป็น dependency ตรง ๆ แต่ browser suite ใช้ `assertNoAccessibilityIssues()` อยู่จริงใน `tests/Browser/SmokeTest.php`

ดังนั้นคำพูดที่แม่นกว่าคือ:

* accessibility automation **มีอยู่จริงใน test workflow**
* แต่ยัง **ไม่ใช่ dedicated accessibility program ที่แยกชั้นและลึกมาก**

### **2.5 Printable evidence convenience remains in scope**

print routes และ print controllers ยังเป็น:

* lightweight evidence surfaces
* no schema change
* no export builder
* no report subsystem

ดังนั้นรอบ evidence convenience ที่ผ่านมา **ยังไม่หลุดกรอบ**

---

## **3. Where External Audits Are Right**

หลายข้อจากข้อความวิเคราะห์ที่คุณแนบมาถูกทิศจริง:

1. โปรเจ็คยังไม่ production-grade
2. docs truth ยังบวมกว่าที่ควร
3. type safety ยังไม่เข้มพอ
4. query scalability ยังไม่ถึงมาตรฐานสูง
5. story alignment ยังไม่ปิดทั้งระบบ
6. ถ้าเปิด feature wave ใหม่เร็วเกิน จะกลับไป scope creep ได้

พวกนี้เป็น **หนี้จริง** ไม่ใช่เรื่องปลอม

---

## **4. Where External Audits Are Now Outdated Or Too Broad**

### **4.1 Docs pruning is not “not started” anymore**

ข้อเท็จจริง:

* `README.md` ถูก prune ไปแล้วรอบหนึ่ง
* `docs/04_Current_State_v1.3.md` ถูก prune ไปแล้วรอบหนึ่ง
* docs ยังไม่ lean สุด แต่ไม่ใช่สถานะเดิมแบบ overgrown หนักทุกชั้นแล้ว

คำพูดที่แม่นกว่าคือ:

**docs pruning started and helped, but canonical entry docs still have room to get sharper**

### **4.2 Story alignment is not limited to welcome/login anymore**

ข้อเท็จจริง:

มีการ align wording เพิ่มแล้วใน:

* dashboard
* daily checklist
* incident detail
* admin user surfaces
* admin template surfaces

คำพูดที่แม่นกว่าคือ:

**story alignment is materially underway, but not fully closed across every surface and every wording layer**

### **4.3 Frontend governance is not just a page anymore**

รอบล่าสุดไม่ได้แค่มี `ui-governance` page แต่ยังปิด defect จริง:

* semantic `dl/dt/dd` bug
* shell contrast issue
* unlabeled sidebar collapse control

ดังนั้น governance ตอนนี้ไม่ใช่แค่ “artifact landed”
แต่คือ:

**artifact + baseline QA + real defect closure**

---

## **5. Real Remaining Technical Debt**

เรียงตามความคุ้มและความสำคัญจริง

### **Debt 1 — Enum casting is still incomplete**

จาก model ปัจจุบัน:

* `User.role` ยังไม่ cast เป็น `UserRole`
* `ChecklistTemplate.scope` ยังไม่ cast เป็น enum
* `Incident.status`, `Incident.severity`, และ `Incident.category` ยังไม่ cast เป็น enum

สิ่งนี้ยังไม่ทำให้ระบบพัง
แต่เป็นหนี้ด้าน:

* correctness
* maintainability
* type clarity
* future safety for filters, comparisons, and refactors

### **Debt 2 — Incident list query still returns full collections**

`app/Application/Incidents/Queries/ListIncidents.php` ยังใช้ `->get()`

สำหรับ capstone baseline:

* ยังรับได้

สำหรับคำว่า scalable/high-standard:

* ยังไม่ดีพอ

นี่คือจุด query hygiene ที่เห็นชัดที่สุด

### **Debt 3 — Dashboard query object is useful but becoming a hotspot**

`GetDashboardSnapshot` ยังทำหน้าที่ได้ถูกทิศและดีกว่า controller มาก
แต่ความจริงคือมันเริ่มรวม:

* counts
* trends
* hotspots
* ownership pressure
* workboard framing
* recent history context

จุดนี้ยังไม่ต้องแตกตอนนี้แบบ over-engineer
แต่ควรถูกมองเป็น **future hotspot** ที่ต้องเฝ้า

### **Debt 4 — Documentation entry layer can still get leaner**

แม้ README และ Current State จะดีขึ้นแล้ว แต่ยังมีโอกาสทำให้:

* อ่าน current truth ได้เร็วขึ้น
* ลดคำอธิบายซ้ำ
* แยก “what the product is now” ออกจาก “how the repo evolved” ให้ชัดขึ้นอีก

### **Debt 5 — Story alignment still has residual wording debt**

ถึง product surfaces หลักจะดีขึ้นแล้ว แต่ยังควรมี residual pass ต่ออีกครั้งกับ:

* token comments / internal wording layers
* help text บางจุด
* admin or workflow microcopy บางหน้า

---

## **6. Current Grade**

### **Fair scoring**

* **capstone completeness**: 86–88%
* **internal product polish**: 78–80%
* **production-grade readiness**: 58–62%

### **Fair summary**

* **solo-dev capstone**: A- to A
* **disciplined MVP+**: A-
* **production-grade product**: not there yet

---

## **7. The Correct Next Wave**

## **The next wave should be:**

**Hardening and Finish Quality Only**

ไม่ใช่:

* feature expansion
* room/lab context feature wave
* analytics/reporting wave
* notification wave
* export growth wave

---

## **8. Hardening Wave Scope**

### **Phase A — Lean Docs Closure**

เป้าหมาย:

* ทำ `README.md` ให้ lean ขึ้นอีกหนึ่งรอบ โดยคงเฉพาะ entry truth ที่จำเป็น
* ทำ `docs/04_Current_State_v1.3.md` ให้เป็น current truth summary ที่คมขึ้นอีก
* ไม่ขยาย docs catalog ใหม่

### **Phase B — Story Alignment Residual Pass**

เป้าหมาย:

* ไล่ microcopy และ residual wording debt ที่ยังไม่กลม
* เน้น authenticated surfaces และ supporting wording layers
* ห้ามเปลี่ยน route, schema, หรือ workflow truth

### **Phase C — Type Hardening**

เป้าหมาย:

* เพิ่ม enum casts ให้ model หลักที่เหมาะสม:
  * `User.role`
  * `ChecklistTemplate.scope`
  * `Incident.status`
  * `Incident.severity`
  * `Incident.category` เฉพาะถ้ามี enum กลางพร้อมรองรับ
* เก็บ regression tests ให้ชัดว่าของเดิมยังทำงานตรงกัน

### **Phase D — Query Hygiene**

เป้าหมาย:

* เริ่มจาก `ListIncidents` ให้ paginate หรือมี bounded result strategy ที่เหมาะกับ current UI
* review query hotspots ที่ชัดเจนโดยไม่แตก architecture เกินความจำเป็น
* ห้าม over-engineer dashboard query decomposition ถ้ายังไม่มี evidence ว่าจำเป็น

### **Phase E — Closure Review**

เป้าหมาย:

* ตรวจว่า hardening wave นี้ปิดจริง
* สรุปว่าควร freeze capstone scope หรือยัง
* ห้ามเปิด wave ใหม่ก่อน closure review จบ

---

## **9. Recommended Action Order**

ลำดับที่คุ้มที่สุด:

1. `Lean Docs Closure`
2. `Story Alignment Residual Pass`
3. `Type Hardening`
4. `Incident List Query Hygiene`
5. `Closure Review`

นี่คือ sequencing ที่ดีที่สุด เพราะ:

* ไม่เปิด feature ใหม่
* เก็บความไม่คมที่เหลืออยู่
* เพิ่มความน่าเชื่อถือเชิงวิศวกรรม
* ไม่ผลัก repo กลับไปบวม

---

## **10. Final Brutal Truth**

สิ่งที่โปรเจ็คนี้ต้องการตอนนี้ไม่ใช่ “ของใหม่เยอะขึ้น”

มันต้องการ:

**finish quality, type safety, query discipline, and leaner truth**

ถ้าทำ wave ถัดไปตามนี้:

* โปรเจ็คจะดูคมขึ้น
* narrative จะนิ่งขึ้น
* codebase จะสะอาดขึ้น
* และคำว่า “disciplined MVP+” จะน่าเชื่อถือขึ้นอีกมาก

ถ้ากระโดดไปเพิ่ม feature ใหม่ตอนนี้:

* repo จะเริ่มกว้างเกิน phase
* docs จะบวมอีก
* QA/governance momentum จะเสีย

### **One-line decision**

**Do not open a new product capability wave yet. Close the remaining hardening debt first.**
