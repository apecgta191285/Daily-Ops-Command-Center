# **Phase 4 Evidence Print Surfaces — Execution Pack**

**DOC-102 | Phase 4 selective product tightening after identity/story alignment**  
**Date:** 2026-04-20  
**Status:** Implemented

## **1. Why This Round Exists**

หลัง identity realignment และ frontend governance baseline ถูกลงระบบแล้ว จุดคุ้มค่าถัดไปไม่ใช่ feature wave ใหม่ แต่คือเพิ่ม convenience ที่ช่วย demo/review จริงโดยไม่ดันระบบให้ไหลไปเป็น export platform หรือ reporting subsystem

ก้อนนี้จึงเลือกทำเพียง:

* printable checklist recap
* printable incident summary

และจงใจไม่ทำ:

* PDF generation
* CSV export
* reporting builder
* analytics export

## **2. Product Decision**

product baseline หลัง round นี้คือ:

* operational history ยังเป็น lightweight review layer เหมือนเดิม
* แต่ management สามารถเปิด print-friendly surfaces ได้เมื่อจำเป็นต้องใช้ recap เป็น evidence pack สำหรับการ review, handoff discussion, หรือ capstone demo
* printable surfaces เป็น convenience layer เท่านั้น ไม่ใช่ product family ใหม่

## **3. Implemented Scope**

### **3.1 Checklist archive**

เพิ่ม route:

* `/checklists/history/{run}/print`

เพิ่ม print-friendly recap surface สำหรับ submitted run โดย reuse archive truth ที่มีอยู่แล้ว:

* run metadata
* scope lane
* summary counts
* grouped submitted answers
* note context

### **3.2 Incident detail**

เพิ่ม route:

* `/incidents/{incident}/print`

เพิ่ม print-friendly incident summary โดย reuse incident truth ที่มีอยู่แล้ว:

* reported context
* accountability snapshot
* latest handling notes
* attachment reference
* activity trail

## **4. Architectural Notes**

* ไม่แตะ schema
* ไม่เปลี่ยน route contract เดิมของ live product pages
* ไม่เพิ่ม persistence ใหม่
* ใช้ management-only boundary เดิม
* แยก print data assembly ไว้ใน dedicated management controllers แทนการยัด logic ลง Blade

## **5. UI / Frontend Notes**

* เพิ่ม print layout แยกจาก authenticated shell เพื่อไม่ให้ Flux shell chrome ปนกับ printable evidence surface
* เพิ่ม print utility contract ใน frontend CSS สำหรับ:
  * print shell
  * print toolbar
  * print header
  * print summary grid
  * print media behavior
* เพิ่ม entry buttons จาก live product surfaces ไปยัง print pages แบบเปิด tab ใหม่

## **6. Regression Proof**

รอบนี้ต้องผ่าน:

* feature tests สำหรับ management-only access
* feature tests สำหรับ printable recap / summary content
* browser smoke proof ว่า core surfaces แสดง printable action ได้โดยไม่เกิด JS smoke
* existing lint / build / browser suites เดิม

## **7. Guard Rails**

รอบนี้ตั้งใจไม่ทำ:

* export queue
* scheduled reports
* reporting filters ขนาดใหญ่
* analytics framing
* tenant/location model

ถ้าจะขยายต่อในอนาคต ให้ขยายเฉพาะ printable/evidence convenience ที่ยัง grounded กับ current product identity เท่านั้น
