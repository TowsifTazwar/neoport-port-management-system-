# NeoPort – Port Management System
**Stack:** PHP (XAMPP/Apache), MySQL/MariaDB, HTML/CSS/JS

NeoPort is a full-stack **Port Management System** that digitizes day-to-day port operations—**shipping requests**, **berth allocations**, **cargo assignments**, **logistics coordination**, and **role-based dashboards**—with **auditable activity logs** and **KPI cards** for decision makers.  
A standout capability is **map-based ship tracking**: **Importers/Exporters** can view a vessel’s **current / last-known location** on a map alongside request status, ETA windows, and berth assignments.

---

## Why NeoPort?
- **Operational clarity:** one system of record for requests, vessels, berths, and cargo.
- **Fewer errors:** structured workflows + foreign keys + validations.
- **Transparency:** **live location view** for Importers/Exporters reduces back-and-forth.
- **Accountability:** all critical actions land in **Activity Logs** with timestamps.
- **Extendable:** modular structure leaves room for notifications, APIs, analytics.

---

## Core Features

### 1) Shipping Requests & Approvals
- Create, submit, approve/reject with notes and timestamps.
- Status pipeline: *draft → submitted → review → approved/rejected → archived*.
- Attach vessel, cargo summary, requested berth window, and documents.

### 2) Berth Management & Scheduling
- Maintain **berth inventory** and allocate dock windows.
- Track **docked / departed**; prevent schedule clashes; monitor utilization.
- Director view shows conflicts, overlaps, and turnaround KPIs.

### 3) Cargo Assignments & Logistics
- Assign cargo to vessels/requests/berths; set handling priorities and storage locations.
- Track handling progress; connect to yard/warehouse movements (extensible).

### 4) Role-Based Dashboards
- **Importer / Exporter:** request status, **live ship location**, ETA, berth window.
- **Customs & Compliance:** checks, flags/holds, clearance history.
- **Logistics / Transport:** tasks, hand-offs, progress, yard pointers.
- **Port Director / Admin:** KPIs, berth occupancy, approvals, activity feed.

### 5) **Live Ship Location (Map)**
- Map view (Leaflet/Google/Mapbox or equivalent) shows **current / last-known** ship position.
- Vessel markers link to **shipping requests, berth slots, and cargo**.
- Access controlled by role: Importer/Exporter can view only their own vessels.

### 6) KPIs, Search, and Auditability
- KPI cards (pending approvals, occupied berths, departures today, etc.).
- Global search & filters (vessel, berth, request, date, status).


---

## Screenshots



| | | |
|---|---|---|
| ![1](screenshots/1.jpg) | ![2](screenshots/2.jpg) | ![3](screenshots/3.jpg) |
| ![4](screenshots/4.jpg) | ![5](screenshots/5.jpg) | ![6](screenshots/6.jpg) |
| ![7](screenshots/7.jpg) | ![8](screenshots/8.jpg) | ![9](screenshots/9.jpg) |
| ![10](screenshots/10.jpg) | ![11](screenshots/11.jpg) | ![12](screenshots/12.jpg) |
| ![13](screenshots/13.jpg) | ![14](screenshots/14.jpg) | ![15](screenshots/15.jpg) |
| ![16](screenshots/16.jpg) | ![17](screenshots/17.jpg) | ![18](screenshots/18.jpg) |

---


## Requirements
- **XAMPP** (Apache + MySQL/MariaDB)  
- **PHP** 7.4+  
- **MySQL/MariaDB** (default 3306; this project often uses **3307**—adjust as needed)

---

## Quick Start (Manual Download)

1. **Download the source**
   - Repo page → green **Code** → **Download ZIP**.
   - Extract; you’ll get `neoport-port-management-system-main` (or similar).

2. **Move to web root**
   - XAMPP → put the folder inside: `C:\xampp\htdocs\neoport-port-management-system`
   - (You can rename the folder; your URL changes accordingly.)

3. **Start XAMPP services**
   - Start **Apache** and **MySQL**.

4. **Create the database & import schema**
   - Open **phpMyAdmin**: `http://localhost/phpmyadmin`
   - Create database **`neoport`** (utf8mb4).
   - **Import** → choose `database/data.sql` → Go.
Finally, visit http://localhost/neoport-port-management-system in your browser
