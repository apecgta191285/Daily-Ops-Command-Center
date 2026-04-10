# **15_Manual_Evidence_Capture_Checklist**

## **Purpose**

checklist สั้นสำหรับเก็บภาพหลักฐานก่อนสอบหรือก่อน present โดยผูกกับ route และหน้าจริงของระบบ

## **1. Baseline First**

ก่อนเก็บหลักฐาน:

```bash
php artisan migrate:fresh --seed
php artisan test
```

## **2. Screens to Capture**

* Login page: `/login`
* Staff landing page: `/checklists/runs/today`
* Staff incident create page: `/incidents/new`
* Supervisor/Admin incident list: `/incidents`
* Incident detail + timeline: `/incidents/{incident}`
* Dashboard summary: `/dashboard`
* Admin template management entry: `/templates`

## **3. Access-Control Proof Screens**

เก็บอย่างน้อย 2 ชุด:

* Staff ถูกบล็อกจาก `/dashboard`
* Staff ถูกบล็อกจาก `/incidents`
* Supervisor เข้า `/incidents` ได้
* Admin เข้า `/templates` ได้

## **4. Content Proof Screens**

* Checklist page ที่มี item ครบและพร้อม submit
* Incident create form ที่มี field ครบตาม scope
* Incident detail ที่เห็น current status และ activity timeline
* Dashboard ที่เห็น completion summary + incident counts + recent incidents

## **5. Evidence Notes to Save Alongside Screenshots**

* วันที่และเวลาที่เก็บหลักฐาน
* account ที่ใช้
* route ที่เปิด
* ถ้าเป็น proof ของ status update ให้จด old status → new status
* แนบผล `php artisan test` ล่าสุดไว้กับ evidence bundle
