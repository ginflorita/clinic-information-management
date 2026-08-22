# DCIMS — Dental Clinic Information Management System

A Laravel-based clinic information system for dental practices: patient records, clinical charting, treatment planning, billing, and inventory in one app.

**Live:** https://clinic-information-management.vercel.app
**Stack:** Laravel 12 (PHP 8.4) · Blade · Bootstrap 5 · PostgreSQL (Supabase) · deployed on Vercel

## Modules

**Core**
- **Auth & Users** — admin-managed accounts (no public self-registration); active/inactive and admin/staff roles
- **Master Data** — clinic reference data (teeth, procedure types, and other lookups used across the app)
- **Patients** — patient records and demographics
- **Medical/Dental History & Allergies** — per-patient history intake and allergy tracking

**Clinical**
- **Appointments & Queue** — scheduling and day-of visit queue
- **Encounters & Clinical Notes** — visit records and provider notes
- **Dental Chart (Odontogram)** — tooth-by-tooth charting
- **Periodontal Charting** — full-mouth perio exams with per-site probing depth, recession, CAL, bleeding/plaque
- **Diagnosis** — per-encounter diagnoses
- **Treatment Planning** — proposed treatment plans with status workflow
- **Procedures & Procedure Records** — procedures performed per encounter

**Financial**
- **Billing** — invoices generated from procedures/treatment plans
- **Payments** — payment recording against invoices
- **Patient Ledger** — running account balance per patient
- **Patient Timeline** — unified chronological view of a patient's clinical and financial activity

**Inventory & Procurement**
- **Inventory Management** — products, suppliers, stock batches, and stock movements with low-stock/expiry tracking
- **Purchase Orders & Goods Receipts** — ordering from suppliers and receiving stock into inventory

**Reporting & Compliance**
- **Dashboard & Reports** — clinic-wide overview and basic reports
- **Audit Trail** — change history (create/update/delete) across auditable records, with actor, IP, and before/after values

## Development

The app lives in [`dcims/`](dcims). See that directory for local setup (Laravel `.env`, `composer install`, `npm install`, `php artisan migrate`).
