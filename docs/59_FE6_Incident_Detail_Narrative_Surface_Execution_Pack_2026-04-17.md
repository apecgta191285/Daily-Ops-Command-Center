# FE6 Incident Detail Narrative Surface Execution Pack

วันที่อ้างอิง: 17/04/2569

## Objective

ยกระดับหน้า incident detail จากหน้าที่ “ข้อมูลครบและแก้ status ได้” ไปสู่หน้าที่ “อ่านลำดับเหตุการณ์และการตัดสินใจได้เร็ว” โดยยังใช้ data contract เดิม

## Design Direction

Industrial Command, Editorial Finish

* primary reading lane ชัด
* ล่าสุดต้องเห็นก่อน
* evidence และ next action แยก lane อ่านง่าย
* timeline ต้องอ่านเป็น sequence ไม่ใช่แค่ card list

## Scope

1. ยก incident header ให้มี stronger narrative framing
2. ทำ latest direction / latest resolution เป็น primary lane ที่เด่นขึ้น
3. ปรับ description / attachment / status update ให้มี layout rhythm ดีกว่าเดิม
4. ทำ activity timeline ให้อ่านเป็น operational story มากขึ้น

## Constraints

* ไม่เพิ่ม schema ใหม่
* ไม่เพิ่ม heavy JS
* ไม่เพิ่ม workflow ใหม่
* ใช้ application contract เดิมให้มากที่สุด

## Acceptance Criteria

* incident detail ยังใช้ route และ Livewire contract เดิม
* latest handling context อ่านได้เร็วขึ้นชัดเจน
* status update form ไม่หลงอยู่ใน flow ของหน้า
* timeline อ่านเป็น sequence ได้ดีขึ้นโดยไม่เสีย accessibility
* tests, lint, build, browser smoke ผ่าน

## Out of Scope

* incident assignment
* threaded comments
* rich attachments gallery
* manager approval workflow

## Expected Outcome

หลัง FE6-B หน้า incident detail ควรดูเหมือน “หน้าควบคุมและเล่าเหตุการณ์” มากขึ้นจริง ไม่ใช่เพียงหน้าดูข้อมูลแล้วกดเปลี่ยนสถานะ
