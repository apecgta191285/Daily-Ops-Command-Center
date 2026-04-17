# FE5 Frontend Identity and Theme Contract Resolution

วันที่อ้างอิง: 17/04/2569

## Objective

ปิดจุดค้างที่สำคัญที่สุดหลัง FE4: frontend ต้องมี identity และ theme contract ที่พูดความจริงเดียวกันทั้ง product ไม่ใช่มี appearance toggle ที่เปิดใช้ได้ใน UI แต่ token system ยังไม่รองรับจริง

## Decisions

- เลือก `one flagship theme` เป็นทางการ
- retire appearance switch ออกจาก supported contract
- เสริม typography identity แบบประหยัดและ maintainable โดยเพิ่ม display font สำหรับ hero, stat, และ section headline
- เก็บ hardcoded visual residue ที่ชัดที่สุดให้กลับมาอยู่ใต้ token/component contract

## Scope

1. ถอด `@fluxAppearance` และ route/settings nav ของ appearance ออก
2. เพิ่ม meta description และ typography identity ใน head/token layer
3. ลด `bg-white`, `bg-white/80`, `bg-slate-100`, `#fff8f8`, `#f8fafc` residue หลัก
4. เพิ่ม desktop table row hover และ file-input/disabled-state polish

## Why This Wave Is Worth It

- ลด contract mismatch ระหว่าง settings UI กับ frontend reality
- กัน design drift ในระยะยาว
- ทำให้ระบบดู intentional ขึ้น โดยไม่บานไปเป็น full multi-theme program
- เหมาะกับ solo dev มากกว่าการแบก dark mode ครึ่งระบบ

## Acceptance Criteria

- ไม่มี appearance route หรือ nav item ใน supported settings flow
- head มี meta description และ font contract ใหม่
- table-heavy surfaces หลักมี row hover ที่สม่ำเสมอ
- hardcoded residue เด่นๆ ถูกลดลงบน welcome, dashboard, incidents, templates, checklist, settings modal
- test, lint, build, browser smoke ผ่าน

## Out of Scope

- full dark mode token implementation
- theme switcher ใหม่
- large visual redesign wave ใหม่ทั้งระบบ

## Result

FE5 ควรทำให้ frontend กลายเป็นระบบที่ “ตัดสินใจแล้ว” มากขึ้น ไม่ใช่ระบบที่ยังเปิดช่องให้ visual contract แตกเพราะ feature ที่ยังไม่พร้อมจริง
