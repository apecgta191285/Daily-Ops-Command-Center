# FE6 Motion and Reveal Orchestration Execution Pack

วันที่อ้างอิง: 17/04/2569

## Objective

เพิ่ม motion layer แบบบางแต่ intentional ให้ product surfaces หลักรู้สึก “ประกอบอย่างมีลำดับ” มากขึ้น โดยยังเคารพ accessibility, reduced motion, และไม่เพิ่ม JS complexity เกินจำเป็น

## Design Direction

Industrial Command, Editorial Finish

* motion ต้องช่วย hierarchy
* ไม่ทำให้หน้าดู gimmick
* reveal ต้องสื่อความสำคัญของ lane ต่าง ๆ
* ใช้ app-owned orchestration แทน scattered animations

## Scope

1. เพิ่ม app-owned reveal contract ใน CSS/JS
2. รองรับ stagger ผ่าน data attributes แทน hardcoded per-screen animation logic
3. apply motion orchestration ให้ dashboard, incident detail, และ template authoring surfaces
4. รองรับ reduced motion อย่างถูกต้อง

## Constraints

* ไม่เพิ่ม animation library
* ไม่เพิ่ม heavy JS framework logic
* ไม่ใช้ motion เพื่อกลบ hierarchy ที่ยังออกแบบไม่พอ
* ทุก reveal ต้อง degrade gracefully เมื่อ JS ไม่พร้อม

## Acceptance Criteria

* product surfaces หลักมี reveal rhythm ที่ตั้งใจและอ่านง่ายขึ้น
* reduced motion users ไม่ถูกบังคับ animation
* Livewire navigation ยัง boot motion ได้ถูกต้อง
* browser smoke, tests, lint, build ผ่าน

## Out of Scope

* parallax
* page transition system เต็มรูปแบบ
* decorative looping animation ทั่วทั้งแอป
* motion personalization

## Expected Outcome

หลัง FE6-D product ควรรู้สึก “ประกอบเป็นระบบ” มากขึ้นอีกขั้น โดย motion ทำหน้าที่เน้น cadence และ reading order แทนที่จะเป็นแค่ของตกแต่ง
