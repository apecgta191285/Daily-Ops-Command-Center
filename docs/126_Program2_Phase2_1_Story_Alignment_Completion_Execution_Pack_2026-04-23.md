# Program 2 / Phase 2.1 — Story Alignment Completion Execution Pack
วันที่: 23 เมษายน 2026

## Scope
รอบนี้เป็น `copy/framing hardening only`
และตั้งใจเก็บ residual wording บน authenticated screens ที่ยังหลงเหลือจากภาษากลุ่ม `operational / pressure / control` ให้สงบและ grounded ขึ้น

## Files Changed
- [dashboard.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/dashboard.blade.php)
- [daily-run.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/staff/checklists/daily-run.blade.php)
- [show.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/management/incidents/show.blade.php)
- [history-index.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/management/incidents/history-index.blade.php)
- [index.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/admin/users/index.blade.php)
- [index.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/admin/checklist-templates/index.blade.php)
- [DashboardTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Feature/DashboardTest.php)

## What Tightened
- dashboard เปลี่ยนคำจาก `ownership pressure / operational hotspots / active operating pressure` ไปเป็นภาษาที่อิงงานจริงของทีมแล็บมากขึ้น
- incident detail เปลี่ยนคำอธิบายจาก `operational signal` ไปเป็น `room issue report`
- incident history เปลี่ยน intro จาก `operational record` ไปเป็น `room issue record`
- staff checklist warning copy เปลี่ยนจาก `operational problem` เป็น `room problem`
- admin user/template surfaces ลดถ้อยคำที่ยัง abstract เกินจริง

## What Was Intentionally Left Untouched
- schema
- routes
- controllers / queries / actions
- CSS architecture
- browser QA wave
- dashboard redesign

## Verification
- `php artisan test --filter=DashboardTest`
- `php artisan test --filter=ProductIdentityAlignmentTest`
- `php artisan test --filter=IncidentHistoryTest`

ทั้งหมดผ่าน

## Brutal Truth
รอบนี้ไม่ได้ทำให้ product เก่งขึ้นเชิง feature
แต่มันทำให้ภาษาของ product family คมขึ้น และลดโอกาสที่ระบบจะหลุดกลับไปใช้ wording แบบ generic ops theater บน authenticated screens
