# **N1 Template Duplication and Iteration Safety Execution Pack**

## *Post-F5 Feature Wave / Execution Slice 1*

**DOC-39-N1-EXEC | วันที่อ้างอิง 16/04/2569**

วัตถุประสงค์: ทำให้ checklist template management ปลอดภัยขึ้นสำหรับการพัฒนาต่อ โดยเพิ่มเส้นทาง `duplicate-before-edit` ที่ชัดเจน แทนการบีบให้ admin แก้ template เดิมโดยตรงเมื่อ template นั้นมีประวัติการใช้งานแล้ว

---

## **1. Problem Statement**

หลัง F1-F5 ระบบใช้งานและเดโมได้ดีขึ้นแล้ว แต่ template management ยังมีจุดเสี่ยงเชิง product และ maintainability อยู่:

* หน้า edit template ยังผลักให้ admin แก้ของเดิมโดยตรงง่ายเกินไป
* template ที่มี run history ถูกป้องกันเฉพาะกรณีลบ item ที่มีประวัติแล้ว แต่ยังไม่มี UX path ที่ชัดว่า “ควร duplicate ก่อน”
* การ iterate template รอบใหม่ยังไม่มี fast path ที่ช่วยให้แตก revision อย่างปลอดภัย

Brutal truth: ถ้าไม่เพิ่ม duplication flow ตอนนี้ การขยาย feature template รอบถัดไปจะมีแรงเสียดทานสูงและเสี่ยงทำให้ historical meaning ของ checklist สับสนขึ้น

---

## **2. Scope**

อยู่ใน scope:

* เพิ่ม route และ action สำหรับ duplicate template
* duplicate แล้วสร้าง copy ใหม่เป็น inactive เสมอ
* copy item structure ทั้งชุดจาก template ต้นทาง
* เพิ่ม UI cue ใน template list และ template edit page
* เพิ่ม regression coverage สำหรับ duplicate contract

ไม่อยู่ใน scope:

* template version graph
* template compare view
* soft delete / archive lifecycle
* branching metadata หรือ semantic versioning

---

## **3. Chosen Design**

แนวทางที่เลือก:

* ใช้ `DuplicateChecklistTemplate` action แยกจาก `SaveChecklistTemplate`
* duplicate เป็น persisted copy ทันที ไม่ใช่แค่ prefill form
* duplicate ใหม่จะเป็น `inactive` เสมอ
* title ใหม่ใช้ pattern `Original Title (Copy)` และเพิ่มเลขถ้าชน
* หน้า edit ของ template ที่มี history หรือ active อยู่จะชี้ชัดว่า duplicate เป็นทางที่ปลอดภัยกว่า

เหตุผลที่เลือก:

* persisted copy เหมาะกับ solo-dev workflow มากกว่า prefill-only เพราะกลับมาทำต่อได้
* inactive-by-default ลดความเสี่ยงไปแก้ live template โดยไม่ตั้งใจ
* แยก action ออกจาก save path ทำให้ logic ชัดและ testable

---

## **4. Acceptance Criteria**

งานนี้ถือว่าสำเร็จเมื่อ:

* admin สามารถ duplicate template ได้จาก list page
* duplicate template ถูก redirect ไปหน้า edit ของ copy ใหม่
* duplicate copy เป็น inactive แม้ source จะ active
* checklist items ถูก copy ครบตาม source template
* duplicate title ไม่ชนแม้ duplicate หลายรอบ
* non-admin ใช้ duplicate route ไม่ได้
* หน้า edit template มี cue เรื่อง safer iteration path

---

## **5. Verification**

ชุด verify ขั้นต่ำ:

* `composer lint:check`
* `php artisan test tests/Feature/AdminSurfaceBoundaryTest.php`
* `php artisan test`

ชุด verify เสริม:

* `composer test:browser`

---

## **6. Follow-on Opportunities**

ถ้าจะต่อยอดจากงานนี้อย่างคุ้มค่า:

* add “created from template X” metadata
* add lightweight change summary between source and duplicate
* add template grouping/filtering หลัง duplication flow นิ่งแล้ว

---

## **7. Decision Summary**

การเพิ่ม template duplication ในรอบนี้เป็นงานที่ `คุ้ม`, `เสี่ยงต่ำ`, และ `เพิ่มคุณค่าเชิง product จริง` เพราะช่วยให้ admin iterate template ได้ปลอดภัยขึ้น โดยไม่ต้องกระโดดไปทำ versioning system เต็มรูปแบบก่อนเวลา
