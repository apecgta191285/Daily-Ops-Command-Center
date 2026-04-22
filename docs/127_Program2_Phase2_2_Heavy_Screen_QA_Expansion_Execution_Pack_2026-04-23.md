# Program 2 / Phase 2.2 — Heavy-Screen QA Expansion Execution Pack
วันที่: 23 เมษายน 2026

## Scope
รอบนี้ขยาย browser QA แบบแคบและ evidence-based
โดยเน้น:
- แยก coverage truth ให้ชัด
- เพิ่ม accessibility assertions เฉพาะจอหนักที่นิ่งพอ
- ไม่ฝืน screenshot gate กับจอที่ยัง render แกว่ง

## Files Changed
- [SmokeTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Browser/SmokeTest.php)
- [README.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Browser/README.md)
- [browser_coverage_matrix.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/browser_coverage_matrix.md)

## What Tightened
- incident queue เพิ่ม `assertNoAccessibilityIssues()`
- incident history เพิ่ม `assertNoAccessibilityIssues()`
- checklist archive recap flow เพิ่ม `assertNoAccessibilityIssues()`
- management dashboard smoke wording ถูก align กับ UI truth ล่าสุด
- browser README ระบุ coverage shorthand และ current heavy-screen stance ชัดขึ้น
- เพิ่ม browser coverage matrix สำหรับอ้างอิงใน hardening program

## What Was Intentionally Left Untouched
- ไม่เพิ่ม screenshot baseline ให้ template authoring
- ไม่เพิ่ม screenshot baseline ให้ incident detail
- ไม่เปิด redesign wave
- ไม่เพิ่ม animation/Storybook/tooling wave

## Why Some Screens Still Stay Below Screenshot Lock
- template authoring มี authoring-state variance สูง
- incident detail มี timeline/detail-rich state ที่ยังไม่ deterministic พอ

## Verification
- browser tests เฉพาะจอที่แก้ต้องผ่าน
- ไม่มีการอ้างว่าจอใด screenshot-locked ถ้า test ไม่ได้เรียก `assertScreenshotMatches()`
