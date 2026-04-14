# **F3 Checklist UX Execution Pack**

## *DOC-34-F3 | Progress visibility, completion feedback, and recent-run context*

**Version v1.0 | Feature execution pack | วันที่อ้างอิง 14/04/2569**

วัตถุประสงค์: ยกระดับหน้า checklist run ให้ช่วยการทำงานจริงมากขึ้นโดยไม่เพิ่ม workflow state ใหม่และไม่ขยาย scope เกิน A-lite baseline

# **1. Execution Goal**

ทำให้หน้า checklist ของ staff:

* เห็นความคืบหน้าระหว่างทำรายการ
* เข้าใจทันทีว่ายังขาดอะไรอีกก่อนกด submit
* มี context จากการส่งล่าสุดของตนเองเพื่อช่วย review แบบ lightweight

# **2. Chosen Scope**

รอบนี้เลือกทำเฉพาะสิ่งที่คุ้มที่สุดและ reversible:

* progress summary: answered / remaining / not-done / percentage
* completion feedback copy ที่เปลี่ยนตาม state ปัจจุบัน
* recent submission context ของ operator คนปัจจุบัน
* success feedback หลัง submit ที่บอกผลลัพธ์ชัดขึ้น

ไม่ทำในรอบนี้:

* draft state
* autosave
* checklist section/grouping redesign
* per-item attachment/comment workflow
* multi-template runtime

# **3. Architectural Placement**

* recent submission context ถูก build ใน `InitializeDailyRun`
* UI state aggregation เช่น progress count ใช้ Livewire component computed state
* persistence contract ของ checklist submission ไม่เปลี่ยน

# **4. Acceptance Criteria**

ถือว่ารอบนี้สำเร็จเมื่อ:

* staff เห็น progress summary ก่อน submit
* staff เห็นว่ามี item ค้างตอบกี่ข้อ
* staff ที่เคยส่ง checklist มาก่อนเห็น recent submission context ของตัวเอง
* success flash หลัง submit บอกได้ว่า run นี้มี item ที่ถูก mark `Not Done` กี่ข้อ
* regression tests ครอบคลุม progress, recent context, และ success feedback

# **5. Risk Notes**

* ห้ามให้ recent history กลายเป็น analytics module
* ห้ามเพิ่ม query/state ที่ทำให้ checklist render ช้าจนเสีย baseline simplicity
* ห้ามสร้างความรู้สึกว่า checklist รองรับหลาย template พร้อมกันใน runtime

# **6. Verification Surface**

* `php artisan test tests/Feature/ChecklistDailyRunTest.php`
* `composer lint:check`
* `composer test:browser`
