A-lite Foundation Documentation Set
Reference date: 06/04/2569

Project
- ระบบเช็กลิสต์งานประจำวันและติดตามเหตุการณ์ผิดปกติสำหรับทีมงานขนาดเล็ก
- Daily Checklist and Incident Tracking System for Small Teams

Current files in this set
1. 00_Project_Lock_v1.1.md
2. 01_Product_Brief_v1.1.md
3. 02_System_Spec_v0.3.md
4. 03_Evaluation_Protocol_v1.1.md
5. 04_Current_State_v1.3.md
6. 05_Decision_Log_v1.3.md
7. 06_Data_Definition_v1.2.md
8. 07_UI_Flow_Wireframe_v1.3.md
9. 08_Test_and_Evidence_Plan_v1.2.md
10. 09_Implementation_Foundation_Plan_v1.2.md
11. 10_PATCH_NOTES_2026-04-03.md
12. 11_Implementation_Task_List_v1.0.md

Recommended usage order
Phase 1 - Core lock
1) 00_Project_Lock_v1.1
2) 01_Product_Brief_v1.1
3) 05_Decision_Log_v1.3

Phase 2 - Buildable foundation
4) 06_Data_Definition_v1.2
5) 02_System_Spec_v0.3
6) 07_UI_Flow_Wireframe_v1.3
7) 09_Implementation_Foundation_Plan_v1.2

Phase 3 - Execution control
8) 11_Implementation_Task_List_v1.0
9) 04_Current_State_v1.3

Phase 4 - Proof and evaluation
10) 03_Evaluation_Protocol_v1.1
11) 08_Test_and_Evidence_Plan_v1.2
12) 10_PATCH_NOTES_2026-04-03.md

How to use this set
- Use Project Lock as the master direction document.
- Use Decision Log as the authority for locked engineering decisions.
- Use Data Definition + System Spec + UI Flow + Implementation Plan before coding.
- Use Implementation Task List as the day-by-day execution order.
- Update Current State regularly during the build.
- Use Evaluation Protocol + Test and Evidence Plan before demo/evaluation.

Current note
- This set is in live implementation phase. MVP slices through Day 5B are completed.
- We have officially pivoted to SQLite for local MVP development to minimize environmental bottlenecks.
- The incident status permission conflict is closed: Admin and Supervisor can update incident status; Staff cannot.
- The WSL runtime baseline is verified and the current full test suite is green.
- This set is demo-ready MVP documentation, not production-ready documentation.

Brutal rule
- Do not add new features to scope unless they are reflected in Project Lock and Decision Log.
- Do not skip the execution order in 11_Implementation_Task_List_v1.0 unless there is a documented blocker.
- Do not claim the project is “ready” until the end-to-end demo path and evidence bundle exist.
