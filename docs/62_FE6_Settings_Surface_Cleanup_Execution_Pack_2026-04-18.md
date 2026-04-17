# FE6 Settings Surface Cleanup Execution Pack

วันที่อ้างอิง: 18/04/2569

## Objective

ยกระดับ settings surfaces ให้เป็น screen family เดียวกับ product หลักอย่างชัดเจนขึ้น ทั้งในเชิง hierarchy, section rhythm, modal consistency, และ navigation clarity โดยไม่เปลี่ยน account/security workflow หลัก

## Design Direction

Industrial Command, Editorial Finish

* settings ต้องอ่านเป็น control surface ที่สงบและชัด
* profile / security / destructive actions ต้องแยกน้ำหนักกันชัดเจน
* modal flows ต้องดู intentional ไม่ใช่ Flux defaults ที่ถูกห่อไว้เฉย ๆ

## Scope

1. ปรับ settings layout ให้มี navigation rail และ section framing ที่ชัดขึ้น
2. ทำ profile/security sections ให้มี card rhythm และ supporting context ที่นิ่งกว่าเดิม
3. เก็บ modal/recovery flows ให้ใช้ language เดียวกันมากขึ้น
4. เพิ่ม smoke coverage สำหรับ settings navigation และ core content

## Constraints

* ไม่เปลี่ยน route contract
* ไม่เปลี่ยน auth/security business logic
* ไม่เพิ่ม heavy JS
* ใช้ app-owned CSS contract เดิมให้มากที่สุด

## Acceptance Criteria

* settings pages อ่านเป็น family เดียวกันกับ product หลัก
* destructive zone, password update, และ two-factor flows มี visual hierarchy ชัดขึ้น
* modal content และ recovery code views ไม่ดูเป็นชิ้นหลุดจากระบบ
* browser smoke, lint, tests, build ผ่าน

## Out of Scope

* settings personalization ใหม่
* role-based settings expansion
* device/session management
* notification preference center

## Expected Outcome

หลัง FE6-E settings surface ควรดู “จบ” มากขึ้นในฐานะ account control area ของระบบ ไม่ใช่แค่หน้าที่ใช้งานได้แต่ยังดูบางและกระจัดกระจายกว่าหน้า product หลัก
