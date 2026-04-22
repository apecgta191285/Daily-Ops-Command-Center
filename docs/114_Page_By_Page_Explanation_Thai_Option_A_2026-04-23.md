# Page-by-Page Explanation Notes (Thai) for Option A
วันที่: 23 เมษายน 2026

## 1) Welcome / Login
หน้านี้ใช้บอกก่อนว่าระบบนี้เป็น internal web app สำหรับทีมดูแลห้องคอมในมหาวิทยาลัย ไม่ใช่ระบบสมัครใช้งานสาธารณะ และใน local demo จะมีบัญชีตัวอย่างให้เห็น role หลักของระบบ

## 2) Staff Checklist Runtime
หน้านี้คือหน้าที่นักศึกษาที่เข้าเวรใช้ทำงานจริง โดยต้องเลือกห้องก่อนเมื่อมีหลายห้อง active จากนั้นจึงทำ checklist ตามช่วงเวลา เช่น opening หรือ closing จุดสำคัญที่ต้องอธิบายคือ checklist ไม่ได้เป็นแค่ฟอร์มเช็กเฉย ๆ แต่เป็นจุดเริ่มต้นของการส่งต่อปัญหาไปยัง incident flow ได้

## 3) Staff Incident Create
หน้านี้ใช้เมื่อนักศึกษาพบปัญหาระหว่างตรวจห้อง สามารถกรอกชื่อเรื่อง รายละเอียด ระดับความรุนแรง ห้องที่เกี่ยวข้อง และ optional equipment reference ได้ จุดสำคัญคือ incident นี้ยังผูกกับ room context อยู่ ไม่ได้เป็นรายการลอย ๆ

## 4) Dashboard
dashboard เป็นหน้าของฝั่ง management ใช้ดูภาพรวมว่าห้องไหนมีงานค้าง มี incident อะไรที่ควรตาม และมีสัญญาณอะไรที่ต้องสนใจ จุดสำคัญที่ต้องพูดคือ dashboard นี้ใช้เพื่อช่วยผู้ดูแลห้องตัดสินใจว่าจะไปตามงานที่ห้องไหนก่อน

## 5) Incident Queue
หน้านี้คือรายการ incident ที่ใช้ติดตามงานต่อ จุดสำคัญคือรายการแต่ละอันมี room context และถ้ามีการกรอก equipment reference ก็จะเห็นร่วมด้วย ทำให้ผู้ดูแลห้องรู้ได้เร็วว่าปัญหาอยู่ห้องไหนและเกี่ยวข้องกับอะไร

## 6) Incident Detail
หน้านี้ใช้ดูรายละเอียด incident รายตัว มีสถานะ ความรุนแรง ห้องที่เกี่ยวข้อง optional equipment reference และ activity timeline จุดสำคัญคือผู้ดูแลห้องใช้หน้านี้เพื่อรับช่วงต่อและอัปเดต accountability หรือ status ของงาน

## 7) Checklist History
หน้านี้ใช้ดูประวัติ checklist run ที่เคยเกิดขึ้นแล้ว โดยตอนนี้ history จะสะท้อน room context ด้วย จุดสำคัญคือเราย้อนดูได้ว่าในวันหนึ่งห้องไหนถูกตรวจแล้ว และมี note อะไรจากการตรวจบ้าง

## 8) Incident History
หน้านี้ใช้ดูประวัติ incident ทั้งที่ยังเปิดอยู่และที่ปิดแล้ว จุดสำคัญคือช่วยให้เห็นว่าห้องไหนมีประวัติปัญหาอะไรบ้างในระดับ room-centered operations

## 9) Printable Recap / Summary
สองหน้านี้ใช้พิมพ์หรือเปิดเป็น recap เพื่อการ review หรือใช้เป็น evidence ตอนเดโม จุดสำคัญคือข้อมูลที่พิมพ์ยังคง room context และ optional equipment reference อยู่ ทำให้เรื่องเล่าของเดโมไม่หลุด

## 10) Template Administration
หน้านี้คือส่วนที่ admin ใช้ดูแล checklist templates จุดสำคัญคือ template ยังยึดโมเดลตามช่วงเวลา `opening / during-day / closing` เหมือนเดิม เราไม่ได้ระเบิด template แยกเป็นรายเครื่องหรือราย asset

## 11) User Administration
หน้านี้คือส่วนที่ admin ใช้ดูแลบัญชีผู้ใช้ จุดสำคัญคืออธิบายให้เห็นว่าระบบนี้ยังเป็น internal-only provisioning และใช้ 3 roles เท่านั้น ไม่ใช่ระบบสมัครใช้งานเอง

## 12) ถ้าจะอธิบายทั้งระบบแบบลากทีละหน้า
เริ่มจาก login เพื่อบอกบริบทและ role  
ต่อไปที่ checklist runtime เพื่อโชว์งานของนักศึกษา  
จากนั้นไป incident create เพื่อโชว์การแจ้งปัญหาพร้อม room context  
ต่อไปที่ dashboard และ incident queue/detail เพื่อโชว์มุมของผู้ดูแลห้อง  
ปิดท้ายที่ template/user admin เพื่อยืนยันว่าการกำกับดูแลยังอยู่ในระบบเดียวกัน

