# FE6 Dashboard Signal Depth Execution Pack

วันที่อ้างอิง: 17/04/2569

## Objective

ยกระดับ dashboard จากหน้าที่ “ถูกต้องและใช้ได้” ไปสู่หน้าที่ “มีแรงดึงสายตา, อ่านสัญญาณได้เร็ว, และมี signature ของ product” โดยไม่เพิ่ม backend analytics layer ใหม่

## Design Direction

Industrial Command, Editorial Finish

* command-oriented
* readable in one glance
* information-weighted
* ไม่หวือหวาแบบ gimmick
* ไม่กลายเป็น chart zoo

## Scope

1. ทำ hero aside ให้เป็น `operational glance rail` ที่อ่านง่ายขึ้น
2. เพิ่ม trend emphasis สำหรับ checklist / incident intake
3. ทำ hotspot section ให้มี rank, intensity, และ scanability ที่ดีกว่า list card เดิม
4. เพิ่ม visual hierarchy ให้ attention cards โดยไม่เพิ่ม complexity ทาง data contract

## Constraints

* ไม่เพิ่ม schema ใหม่
* ไม่เพิ่ม chart library
* ไม่เพิ่ม heavy JS
* ใช้ data ที่ dashboard snapshot มีอยู่แล้ว หรือ derive ใน view ได้แบบบาง

## Acceptance Criteria

* dashboard ยังใช้ route/data contract เดิมได้
* dashboard อ่านเร็วขึ้นชัดเจนใน 3 ส่วน: hero, trends, hotspots
* visual hierarchy ชัดขึ้นโดยไม่ทำให้หน้าแน่นเกิน
* tests, lint, build, browser smoke ผ่าน

## Out of Scope

* long-range analytics
* historical charts หลายสัปดาห์
* heatmap หรือ visualization library
* dashboard personalization

## Expected Outcome

หลัง FE6-A dashboard ควรดูเหมือน “หน้าหลักของระบบ” มากขึ้นจริง ไม่ใช่แค่หน้ารวม card และ table ที่ถูกต้อง
