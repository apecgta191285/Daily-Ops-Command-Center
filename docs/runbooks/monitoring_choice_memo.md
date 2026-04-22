# Monitoring Choice Memo
วันที่: 23 เมษายน 2026

## Decision
สำหรับ `production v1` ให้เริ่มจาก:
- `daily` file logs เป็น operational baseline
- manual log review + release smoke review เป็น minimum discipline
- external monitoring tool เป็น `next-step recommendation`, ไม่ใช่ current landed truth

## Why This Decision
1. ตรงกับ repo truth ปัจจุบันที่สุด
2. ไม่อ้าง integration ที่ยังไม่มี
3. เหมาะกับ single-node baseline หลัง defense
4. ทำให้ phase นี้ปิดแบบซื่อสัตย์ได้ก่อน

## Recommended Next Integration
หากจะเสริมหลังจากนี้ ให้เลือก error aggregation เพียง 1 ตัวก่อน เช่น:
- Sentry
- Bugsnag
- equivalent managed error monitoring

แต่การเลือก/ติดตั้งต้องเป็น future hardening task แยก

## Alternatives Considered
### 1. Claim “Laravel daily logs is enough”
ปฏิเสธในเชิง long-term
เพราะเพียงพอแค่ baseline เริ่มต้น ไม่ใช่ mature monitoring stance

### 2. Force full monitoring suite now
ปฏิเสธ
เพราะจะเปิด integration wave ใหม่เกินขอบเขต phase นี้

## Honest Conclusion
production v1 หลัง phase นี้จะมี:
- observability baseline
- monitoring choice
- triage discipline

แต่ยังไม่มี:
- integrated monitoring proof
- alert delivery proof
- incident response timing evidence
