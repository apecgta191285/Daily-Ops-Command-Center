# FE6 Template Authoring Surface Depth Execution Pack

วันที่อ้างอิง: 17/04/2569

## Objective

ยกระดับหน้า template authoring จากหน้าที่ “แก้ฟิลด์ได้และเซฟได้” ไปสู่หน้าที่ “ช่วยคิด, ช่วยจัดลำดับ, และช่วยมองผลกระทบก่อนเปลี่ยน live checklist” โดยไม่เพิ่ม workflow ใหม่หรือ schema ใหม่

## Design Direction

Industrial Command, Editorial Finish

* authoring lane ชัด
* preview ต้องอ่านเป็น daily run ได้
* governance ต้องเด่นพอให้ admin หยุดคิดก่อน activate
* form rhythm ต้องดูเป็น workspace ไม่ใช่ฟอร์มยาวล้วน

## Scope

1. เพิ่ม authoring rhythm / checkpoint framing ที่หน้า template manage
2. เพิ่ม live execution preview สำหรับ grouped checklist items
3. ยก item cards ให้มี identity, chips, และ execution cue ชัดขึ้น
4. ทำ summary lane ให้ admin scan จำนวน item / sections / required coverage ได้เร็วขึ้น

## Constraints

* ไม่เพิ่ม schema ใหม่
* ไม่เพิ่ม heavy JS
* ไม่เปลี่ยน save/duplicate/runtime contract
* ใช้ support classes และ Livewire contract เดิมให้มากที่สุด

## Acceptance Criteria

* template manage page ยังใช้ route และ save contract เดิม
* authoring page อ่านเป็น workspace ที่มี guidance ชัดขึ้น
* admin มอง preview ของ daily checklist ได้โดยไม่ต้องจินตนาการจากฟิลด์ดิบอย่างเดียว
* activation impact และ revision safety ยังเด่นและเข้าบริบทกว่าเดิม
* tests, lint, build, browser smoke ผ่าน

## Out of Scope

* template version history เต็มรูปแบบ
* drag-and-drop builder
* nested checklist sections
* live collaborative editing

## Expected Outcome

หลัง FE6-C หน้า template administration ควรดูเหมือน “พื้นที่ authoring ที่พร้อมใช้จริง” มากขึ้น ไม่ใช่แค่หน้าแก้รายการ checklist ทีละ field แบบแบนๆ
