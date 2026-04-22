# Browser Coverage Matrix
วันที่: 23 เมษายน 2026

## Purpose
เอกสารนี้ใช้แยกให้ชัดว่า browser-level coverage ของแต่ละ surface อยู่ระดับไหน
เพื่อกันการ overclaim ว่า “ทุกจอถูก screenshot lock แล้ว” ทั้งที่ความจริงยังไม่ใช่

## Coverage Levels
### Smoke only
- ไม่มี JavaScript error
- ไม่มี console noise
- มี core content ตามที่คาด

### Smoke + accessibility
- ผ่าน smoke checks
- ผ่าน `assertNoAccessibilityIssues()`

### Screenshot-locked
- ผ่าน smoke checks
- มี `assertScreenshotMatches()`
- ใช้ได้เฉพาะจอที่ render state คงที่พอ

## Current Matrix
| Surface | Coverage | Note |
|---|---|---|
| Home | screenshot-locked | desktop + mobile |
| Login | screenshot-locked | desktop + mobile |
| Dashboard | screenshot-locked | desktop + mobile authenticated baseline |
| Checklist runtime | screenshot-locked | desktop + mobile authenticated baseline |
| UI governance | screenshot-locked + accessibility | deterministic admin-only surface |
| Incident queue | smoke + accessibility | queue interactions stable enough for axe checks |
| Incident history | smoke + accessibility | history slices stable enough for axe checks |
| Checklist archive recap flow | smoke + accessibility | archive browse + recap flow checked, not screenshot-locked |
| Template authoring | smoke only | authoring render still too variable for stable screenshot gate |
| Incident detail | smoke only | detail/timeline state still too variable for stable screenshot gate |

## Honest Limitation
matrix นี้ยังไม่แปลว่า authenticated heavy screens ทั้งหมดถูก visual lock แล้ว

จอที่ยังไม่ควรถูก overclaim:
- template authoring
- incident detail

ถ้าจะยกระดับสองจอนี้ในอนาคต ต้องทำ deterministic harness เพิ่มก่อน
