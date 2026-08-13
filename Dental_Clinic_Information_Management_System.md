# Dental Clinic Information Management System
## System Architecture, Functional Modules, PostgreSQL Database Design & Development Checklist

---

# 1. System Overview

The system should function as a **Dental Clinic Information Management System (DCIMS)** combining:

- Patient Information Management
- Dental Electronic Health Records
- Dental Charting / Odontogram
- Medical & Dental History
- Appointment Management
- Patient Queue Management
- Clinical Encounter Management
- Treatment Planning
- Treatment/Procedure Recording
- Periodontal Charting
- Prescription Management
- Dental Imaging / Attachments
- Consent Management
- Billing
- Payments
- Patient Ledger
- Inventory
- Suppliers
- Purchase Management
- Dental Laboratory Management
- Referrals
- Notifications
- Reports
- Audit Trail
- System Configuration
- Authentication (basic login from MVP Phase 1) & Role-Based Access (granular, Phase 3)

The system should be designed around the following central relationship:

```text
PATIENT
   │
   ├── Appointments
   │
   ├── Encounters / Visits
   │       │
   │       ├── Clinical Notes
   │       ├── Dental Chart
   │       ├── Procedures
   │       ├── Diagnoses
   │       ├── Prescriptions
   │       ├── Periodontal Records
   │       ├── Images / Files
   │       └── Consents
   │
   ├── Treatment Plans
   │       └── Treatment Plan Items
   │
   ├── Billing
   │       ├── Invoices
   │       ├── Payments
   │       └── Adjustments
   │
   └── Patient Documents
```

---

# 2. Recommended System Architecture

Use a layered architecture:

```text
┌──────────────────────────────────────┐
│              FRONTEND                │
│                                      │
│ Dashboard                            │
│ Patient Management                   │
│ Dental Chart                         │
│ Appointments                         │
│ Treatment Planning                   │
│ Billing                              │
│ Inventory                            │
└──────────────────┬───────────────────┘
                   │
                   ▼
┌──────────────────────────────────────┐
│             APPLICATION              │
│                                      │
│ Controllers / Services               │
│ Validation                           │
│ Business Rules                       │
│ Transactions                         │
│ File Management                      │
│ Audit Logging                        │
└──────────────────┬───────────────────┘
                   │
                   ▼
┌──────────────────────────────────────┐
│               DATABASE               │
│              PostgreSQL              │
│                                      │
│ Patient                              │
│ Clinical                             │
│ Scheduling                           │
│ Treatment                            │
│ Billing                              │
│ Inventory                            │
│ Documents                            │
│ Audit                                │
└──────────────────────────────────────┘
```

Do **not** put everything into one `patients` table.

For example, avoid:

```text
patients
--------------------------------
name
age
address
medical_history
allergies
tooth_11
tooth_12
tooth_13
...
treatment_1
treatment_2
...
payment_1
payment_2
...
```

That becomes extremely difficult to maintain.

Instead:

```text
patients
medical_histories
patient_allergies
patient_conditions
appointments
encounters
odontograms
tooth_conditions
treatment_plans
treatment_plan_items
procedures
procedure_records
invoices
invoice_items
payments
...
```

---

# 3. Core Modules

## Module 01 — Dashboard

### Purpose

Provide a real-time overview of clinic operations.

### Functions

- Today's appointments
- Today's patients
- Waiting patients
- Currently treating patients
- Completed appointments
- Cancelled appointments
- No-show appointments
- Today's revenue
- Outstanding balances
- New patients
- Pending treatment plans
- Follow-up patients
- Low-stock items
- Expiring inventory
- Recent activities

### CRUD

| Function | Support |
|---|---|
| Create | No |
| Read | Yes |
| Update | No |
| Delete | No |

Dashboard is primarily a reporting/read-only module.

---

# 4. Patient Management

## Module 02 — Patient Registration

### Patient information

Store:

- Patient number
- First name
- Middle name
- Last name
- Suffix
- Preferred name
- Date of birth
- Sex
- Civil status
- Contact number
- Email
- Address
- Occupation
- Emergency contact
- Guardian
- Referral source
- Registration date
- Patient status

### Functions

- Register patient
- Search patient
- View patient profile
- Edit patient information
- Archive patient
- Restore patient
- Merge duplicate patients
- Generate patient number
- Print patient information
- Export patient record

### CRUD

```text
CREATE
READ
UPDATE
DELETE/ARCHIVE
```

### Important rule

Do not physically delete a patient if the patient already has clinical records.

Use:

```text
status = active
status = inactive
status = archived
```

instead.

---

# 5. Patient Relationships

## Module 03 — Patient / Guardian / Family Relationships

Useful especially for:

- Pediatric patients
- Family accounts
- Parents/guardians
- Dependents

### Functions

- Add guardian
- Assign guardian
- Add emergency contact
- Link family members
- View family members
- Remove relationship

Example:

```text
Juan Dela Cruz
    │
    ├── Maria Dela Cruz
    └── Pedro Dela Cruz
```

---

# 6. Medical History

## Module 04 — Medical History

### Functions

Record:

- Existing medical conditions
- Previous surgeries
- Hospitalization
- Current medications
- Allergies
- Pregnancy status where clinically appropriate
- Smoking/tobacco history
- Alcohol history where appropriate
- Family medical history
- Physician information
- Medical alerts

### Important design principle

Do not store everything in one text field:

```text
medical_history TEXT
```

Instead use structured records.

Example:

```text
patient_conditions
patient_allergies
patient_medications
patient_medical_history
```

This allows reporting later.

### CRUD

```text
CREATE
READ
UPDATE
DELETE
```

But clinical history should preferably be **versioned rather than silently overwritten**.

---

# 7. Dental History

## Module 05 — Dental History

Record:

- Previous dental treatment
- Previous dentist
- Previous extraction
- Previous root canal
- Prosthetic history
- Orthodontic history
- Previous dental surgery
- Previous complications
- Dental habits
- Oral hygiene information
- Chief dental concerns

CRUD:

```text
CREATE
READ
UPDATE
DELETE
```

---

# 8. Allergy Management

## Module 06 — Allergies

Examples:

- Penicillin
- Amoxicillin
- Latex
- Local anesthetic
- NSAIDs
- Food allergies

Fields:

```text
allergen
reaction
severity
onset_date
notes
status
```

CRUD:

```text
CREATE
READ
UPDATE
DELETE
```

Important:

Allergy information should be highly visible during clinical treatment.

---

# 9. Appointment Management

## Module 07 — Appointment Management

This is one of the major modules.

### Appointment types

Examples:

- Consultation
- Cleaning
- Extraction
- Filling
- Root canal
- Crown
- Denture
- Orthodontic
- Follow-up
- Surgery
- Emergency

### Functions

- Create appointment
- Reschedule
- Cancel
- Confirm
- Check-in
- Mark arrived
- Mark waiting
- Start treatment
- Complete treatment
- Mark no-show
- Assign dentist
- Assign chair
- Add appointment notes
- View calendar
- Day/week/month calendar
- Appointment history

### Appointment statuses

```text
scheduled
confirmed
arrived
waiting
in_progress
completed
cancelled
no_show
rescheduled
```

### CRUD

```text
CREATE
READ
UPDATE
DELETE/CANCEL
```

Do not physically delete historical appointments.

---

# 10. Chair Management

## Module 08 — Dental Chair / Room Management

For clinics with multiple chairs.

Example:

```text
Chair 01
Chair 02
Chair 03
Chair 04
```

Functions:

- Add chair
- Edit chair
- Activate/deactivate chair
- Assign appointment to chair
- View chair availability

CRUD:

```text
CREATE
READ
UPDATE
DELETE/DEACTIVATE
```

---

# 11. Queue Management

## Module 09 — Patient Queue

Workflow:

```text
CHECK-IN
   ↓
WAITING
   ↓
CALLED
   ↓
IN TREATMENT
   ↓
COMPLETED
```

Functions:

- Add patient to queue
- Change queue position
- Call patient
- Start consultation
- Start treatment
- Complete visit
- Skip patient
- Recall patient

---

# 12. Clinical Encounter

## Module 10 — Patient Visit / Encounter

This should be the **central clinical transaction**.

Every actual clinic visit should create an encounter.

Example:

```text
Patient
   ↓
Appointment
   ↓
Encounter
   ├── Clinical Notes
   ├── Diagnosis
   ├── Dental Chart
   ├── Procedures
   ├── Prescription
   ├── Consent
   ├── Imaging
   └── Billing
```

### Encounter information

- Patient
- Dentist
- Appointment
- Visit date/time
- Chief complaint
- Assessment
- Diagnosis
- Treatment performed
- Clinical notes
- Follow-up
- Disposition
- Encounter status

CRUD:

```text
CREATE
READ
UPDATE
```

For completed encounters, avoid DELETE.

---

# 13. Clinical Notes

## Module 11 — Clinical Documentation

Support:

- SOAP notes
- Progress notes
- Examination notes
- Procedure notes
- Follow-up notes
- Patient communication notes
- Post-operative notes

Example:

```text
Subjective
Objective
Assessment
Plan
```

### Important

Every clinical note should record:

```text
created_by
created_at
updated_by
updated_at
```

And preferably:

```text
signed_by
signed_at
```

Once signed, clinical notes should become immutable.

Corrections should create an amendment rather than silently changing history.

Dental records are treated as important legal/clinical documents, and dental recordkeeping guidance emphasizes accurate documentation and identifying/signing the person making entries.

---

# 14. Dental Chart / Odontogram

## Module 12 — Dental Charting

This is one of the most important dental-specific modules.

The system should support:

- Permanent dentition
- Primary/mixed dentition
- Tooth numbering
- Tooth status
- Tooth conditions
- Surfaces
- Existing restorations
- Missing teeth
- Extraction
- Caries
- Crown
- Bridge
- Implant
- Root canal
- Denture
- Other conditions

---

# 15. Tooth Model

Do NOT create:

```text
tooth_11
tooth_12
tooth_13
...
```

Instead:

```text
teeth
```

Example:

```text
id
notation_system
tooth_code
tooth_name
dentition_type
arch
position
```

This allows:

```text
Universal
FDI
Palmer
```

without redesigning the database.

---

# 16. Tooth Conditions

Use a transaction/history model.

Example:

```text
patient
   ↓
patient_tooth_records
   ↓
tooth_conditions
```

Possible conditions:

```text
healthy
caries
missing
fractured
restored
crown
bridge
implant
root_canal
extracted
impacted
unerupted
mobility
```

Do not store these as a single permanent field on `patients`.

---

# 17. Tooth Surfaces

Dental surfaces should be structured.

Possible surfaces:

```text
M = Mesial
D = Distal
O = Occlusal
I = Incisal
B = Buccal
L = Lingual
P = Palatal
F = Facial
```

You can model:

```text
tooth_surfaces
```

and:

```text
tooth_condition_surfaces
```

This makes restorations and caries location queryable.

---

# 18. Odontogram History

Do not overwrite the previous chart.

Example:

```text
2026-08-01
Tooth 36 = caries

2026-08-13
Tooth 36 = restored

2027-01-10
Tooth 36 = crown
```

This gives you a clinical timeline.

---

# 19. Diagnosis

## Module 13 — Diagnosis Management

Functions:

- Add diagnosis
- Assign diagnosis to encounter
- Assign diagnosis to tooth
- Add diagnosis notes
- Update diagnosis status
- View diagnosis history

Possible statuses:

```text
active
resolved
historical
suspected
```

You can later add standardized coding.

Recommended future capability:

```text
ICD-10
SNOMED CT
Dental procedure codes
```

Do not hard-code diagnostic descriptions into application logic.

Create a terminology table.

---

# 20. Treatment Planning

## Module 14 — Treatment Plans

This should be separate from completed treatment.

A treatment plan represents:

> What the dentist proposes.

A procedure record represents:

> What was actually performed.

### Treatment Plan

```text
Patient
   ↓
Treatment Plan
   ├── Item 1 - Extraction
   ├── Item 2 - Filling
   ├── Item 3 - Crown
   └── Item 4 - Cleaning
```

### Treatment plan status

```text
draft
presented
accepted
partially_accepted
declined
completed
expired
cancelled
```

### Treatment plan item

Store:

- Procedure
- Tooth
- Surface
- Quantity
- Estimated fee
- Discount
- Estimated patient share
- Notes
- Priority
- Status

---

# 21. Treatment Acceptance

## Module 15 — Treatment Consent / Acceptance

Functions:

- Present treatment plan
- Record patient acceptance
- Record rejection
- Record partial acceptance
- Record date/time
- Record consent document
- Record signature/reference

Treatment planning guidance commonly recommends documenting the patient's understanding and acceptance of the proposed treatment.

---

# 22. Procedure Management

## Module 16 — Dental Procedures

Create a master table:

```text
procedures
```

Examples:

```text
Dental Consultation
Oral Prophylaxis
Tooth Extraction
Composite Filling
Amalgam Filling
Root Canal Treatment
Crown
Bridge
Denture
Implant
Orthodontic Adjustment
Fluoride Treatment
X-Ray
```

Each procedure should have:

```text
code
name
description
category
default_fee
duration
active
```

CRUD:

```text
CREATE
READ
UPDATE
DELETE/DEACTIVATE
```

---

# 23. Procedure Records

## Module 17 — Completed Treatments

This is different from the procedure master.

Example:

```text
procedure:
Composite Filling

procedure_record:
Patient = Juan
Tooth = 36
Surface = O
Dentist = Dr. Santos
Date = 2026-08-13
Status = completed
```

Store:

- Encounter
- Procedure
- Tooth
- Surface
- Dentist
- Quantity
- Fee
- Notes
- Start time
- End time
- Status

---

# 24. Periodontal Charting

## Module 18 — Periodontal Management

This should be a separate module.

Track:

- Probing depth
- Gingival recession
- Clinical attachment level
- Bleeding on probing
- Plaque
- Mobility
- Furcation
- Gingival margin
- Pocket depth
- Tooth
- Surface/site
- Examination date

Example:

```text
Patient
  ↓
Perio Examination
  ↓
Tooth
  ↓
Site
  ├── Mesial
  ├── Mid
  └── Distal
```

Do not store periodontal measurements directly in `patients`.

---

# 25. Prescription Management

## Module 19 — Prescriptions

Functions:

- Create prescription
- Add medications
- Dose
- Frequency
- Route
- Duration
- Quantity
- Instructions
- Refills
- Print prescription
- Cancel prescription

Structure:

```text
prescriptions
   ↓
prescription_items
   ↓
medications
```

This prevents duplicated medication information.

---

# 26. Medication Master

## Module 20 — Medication Management

Store:

```text
generic_name
brand_name
dosage_form
strength
unit
active
```

Examples:

```text
Amoxicillin
Ibuprofen
Paracetamol
Chlorhexidine
```

Do not make medications hard-coded in PHP/JavaScript.

---

# 27. Dental Imaging

## Module 21 — Imaging

Support:

- Intraoral photographs
- Extraoral photographs
- Periapical X-ray
- Bitewing X-ray
- Panoramic X-ray
- Cephalometric image
- CBCT reference
- Other diagnostic images

Store metadata in PostgreSQL:

```text
patient
encounter
image_type
tooth
description
captured_at
file_reference
mime_type
file_size
```

Prefer storing the actual large image files in object/file storage rather than placing huge binary files directly in normal transactional tables.

---

# 28. Document Management

## Module 22 — Patient Documents

Documents:

- Consent forms
- Medical history forms
- Dental history forms
- Referral letters
- Laboratory documents
- X-ray reports
- Treatment documents
- Insurance documents
- Identification documents
- Other attachments

Each document should have:

```text
patient_id
encounter_id
document_type
file_name
storage_key
mime_type
file_size
uploaded_by
uploaded_at
```

---

# 29. Consent Management

## Module 23 — Consent

Create:

```text
consent_types
consents
```

Examples:

- Treatment consent
- Extraction consent
- Surgery consent
- Data privacy consent
- Photography consent
- Imaging consent
- Procedure-specific consent

Record:

```text
patient
consent_type
version
status
signed_at
signed_by
document_reference
```

---

# 30. Referral Management

## Module 24 — Referrals

Functions:

- Create referral
- Referring doctor
- Receiving specialist
- Reason
- Referral date
- Clinical summary
- Attachments
- Referral status
- Referral response

Statuses:

```text
draft
sent
received
completed
cancelled
```

---

# 31. Dental Laboratory

## Module 25 — Laboratory Orders

Useful for:

- Crown
- Bridge
- Denture
- Implant restoration
- Orthodontic appliances
- Other laboratory work

Track:

```text
lab
patient
case
procedure
sent_date
expected_date
received_date
status
cost
notes
```

Statuses:

```text
pending
sent
in_progress
ready
received
cancelled
```

---

# 32. Billing

## Module 26 — Billing / Invoicing

Keep financial information separate from clinical records. Dental recordkeeping guidance specifically distinguishes financial records from the clinical record.

### Invoice

```text
invoice
   ↓
invoice_items
```

Invoice items can reference:

```text
procedure
treatment_plan_item
product
laboratory charge
other service
```

---

# 33. Payment Management

## Module 27 — Payments

Support:

- Cash
- Bank transfer
- GCash
- Maya
- Card
- Check
- Other

Fields:

```text
payment_date
amount
payment_method
reference_number
received_by
notes
```

Never modify an old payment to "fix" accounting history.

Use:

```text
refund
void
adjustment
reversal
```

transactions.

---

# 34. Patient Ledger

## Module 28 — Patient Account Ledger

Show:

```text
DATE        DESCRIPTION       DEBIT      CREDIT      BALANCE
-------------------------------------------------------------
Aug 01      Cleaning          800        0           800
Aug 01      Payment           0          500         300
Aug 13      Filling           1200       0           1500
```

The ledger should be generated from transactions rather than manually maintained wherever possible.

---

# 35. Discounts

## Module 29 — Discounts

Support:

- Fixed amount
- Percentage
- Promotional discount
- Senior citizen discount where applicable
- PWD discount where applicable
- Manual adjustment

Every discount should record:

```text
discount_type
amount
reason
approved_by
created_at
```

---

# 36. Inventory

## Module 30 — Inventory Management

Dental clinics commonly need inventory tracking.

Track:

- Dental materials
- Gloves
- Masks
- Syringes
- Anesthetic
- Composite
- Cement
- Impression materials
- Sterilization supplies
- Office supplies
- Medicines

### Functions

- Product CRUD
- Stock-in
- Stock-out
- Adjustment
- Transfer
- Expiry monitoring
- Low-stock monitoring
- Batch tracking

---

# 37. Batch / Expiry Management

## Module 31 — Inventory Batch

For medical/dental supplies, consider:

```text
product
batch_number
lot_number
expiry_date
quantity
unit_cost
supplier
```

This is important for traceability.

---

# 38. Supplier Management

## Module 32 — Suppliers

Store:

```text
supplier
contact_person
phone
email
address
tax_information
status
```

CRUD:

```text
CREATE
READ
UPDATE
DELETE/ARCHIVE
```

---

# 39. Purchase Orders

## Module 33 — Purchasing

Workflow:

```text
Purchase Request
      ↓
Purchase Order
      ↓
Goods Received
      ↓
Inventory Updated
      ↓
Supplier Invoice
```

Tables:

```text
purchase_orders
purchase_order_items
goods_receipts
goods_receipt_items
```

---

# 40. Notifications

## Module 34 — Notifications

Future support:

- Appointment reminder
- Follow-up reminder
- Treatment reminder
- Payment reminder
- Birthday
- Recall
- Missed appointment
- Prescription notification

Channels:

```text
SMS
Email
In-app
```

---

# 41. Recall Management

## Module 35 — Dental Recall

Examples:

```text
6-month cleaning
3-month periodontal follow-up
Post-extraction follow-up
Root canal follow-up
Orthodontic appointment
```

Store:

```text
patient
recall_type
due_date
completed_date
status
notes
```

---

# 42. Reports

## Module 36 — Reporting

### Patient reports

- Patient list
- New patients
- Active patients
- Inactive patients
- Patient demographics

### Appointment reports

- Daily appointments
- Monthly appointments
- Completed
- Cancelled
- No-show
- Rescheduled

### Clinical reports

- Procedures performed
- Treatment plans
- Accepted treatments
- Declined treatments
- Diagnoses
- Tooth conditions

### Financial reports

- Daily sales
- Monthly revenue
- Payments
- Outstanding balances
- Discounts
- Refunds
- Revenue by dentist
- Revenue by procedure

### Inventory reports

- Current stock
- Low stock
- Expiring items
- Stock movement
- Purchases

---

# 43. Audit Trail

## Module 37 — Audit Logging

This is extremely important for a clinical system.

Record:

```text
who
what
when
where
before
after
```

Example:

```text
User:
Dr. Santos

Action:
UPDATE

Table:
patient_tooth_records

Record:
12345

Before:
healthy

After:
caries

Timestamp:
2026-08-13 10:22:31
```

Audit events:

```text
CREATE
UPDATE
DELETE
LOGIN
LOGOUT
VIEW
EXPORT
PRINT
DOWNLOAD
SIGN
VOID
REFUND
```

Design the audit table before you need it — `actor_id` should be ready to reference `users.id` as soon as basic login ships in Phase 1 (§96, §112).

---

# 44. Authentication

## Module 38 — Authentication

A minimal version (single login, no roles) is required starting in MVP Phase 1 (§96) — real patient data shouldn't sit behind an unauthenticated app even during early development. Granular roles/permissions can still wait for Phase 3.

Schema, current and future:

```text
users
roles
permissions
user_roles
role_permissions
sessions
```

Possible roles:

```text
Administrator
Dentist
Dental Assistant
Receptionist
Cashier
Inventory Staff
Clinic Manager
```

---

# 45. Role-Based Access Control

Example:

| Module | Dentist | Receptionist | Cashier | Admin |
|---|---:|---:|---:|---:|
| Patient | CRUD | CRUD | R | CRUD |
| Medical History | CRUD | R | - | R |
| Dental Chart | CRUD | R | - | R |
| Treatment | CRUD | R | - | R |
| Billing | R | CRUD | CRUD | CRUD |
| Payments | R | R | CRUD | CRUD |
| Inventory | R | - | - | CRUD |
| Reports | R | R | R | CRUD |
| Audit | - | - | - | R |

Do not implement permissions only in the frontend.

Authorization must eventually be enforced on the backend.

---

# 46. Security Architecture

Build the application with security in mind from the start, not just once granular RBAC (Phase 3) arrives.

## Application Security

- [ ] Server-side validation
- [ ] Input validation
- [ ] Output escaping
- [ ] SQL injection protection
- [ ] Parameterized queries
- [ ] CSRF protection
- [ ] XSS protection
- [ ] Secure file upload
- [ ] MIME type validation
- [ ] File extension validation
- [ ] File size limits
- [ ] Path traversal protection
- [ ] Rate limiting
- [ ] Error handling
- [ ] Secure headers
- [ ] HTTPS
- [ ] Database least privilege
- [ ] Backup encryption
- [ ] Encryption in transit
- [ ] Encryption at rest where appropriate

The Philippine Data Privacy Act requires reasonable and appropriate organizational, physical, and technical safeguards for personal information. NPC guidance also emphasizes confidentiality, integrity, availability, network safeguards, monitoring, and security programs.

---

# 47. File Upload Security

Never do:

```text
/upload/{original_filename}
```

Instead generate:

```text
UUID
```

Example:

```text
3f8c5e1a-....
```

Store:

```text
original_filename
storage_key
mime_type
size
hash
```

Recommended:

```text
Database
    ↓
file metadata

Object/File Storage
    ↓
actual file
```

---

# 48. Database Security

Create separate PostgreSQL users.

Example:

```text
dcims_app
dcims_readonly
dcims_backup
```

The application should NOT connect using the PostgreSQL superuser.

---

# 49. PostgreSQL Schema Organization

I recommend PostgreSQL schemas:

```text
core
clinical
scheduling
billing
inventory
documents
system
```

Example:

```text
core.patients

clinical.encounters

scheduling.appointments

billing.invoices

inventory.products

documents.files

system.audit_logs
```

This provides much better organization as the system grows.

---

# 50. Database Naming Convention

Use:

```text
snake_case
```

Tables:

```text
patients
appointments
treatment_plans
```

Primary keys:

```text
id
```

Foreign keys:

```text
patient_id
appointment_id
encounter_id
```

Dates:

```text
created_at
updated_at
deleted_at
```

Exception: `patients` (§61) uses `archived_at` instead of `deleted_at`, because "archived" and "deleted" are meant to read as different concepts for clinical data — a patient is never actually deleted (§83), only archived, and `status` already carries `active`/`inactive`/`archived`. Use `deleted_at` for genuinely deletable master data (e.g. a mistakenly-created `procedure_categories` row); use `archived_at` (or another domain-specific name) anywhere the record is being retired rather than removed. Don't let both conventions drift into the same table.

Boolean:

```text
is_active
is_deleted
```

Avoid:

```text
PatientID
PatientName
tblPatients
tbl_patient_info
```

---

# 51. Recommended PostgreSQL Data Types

Use:

```text
BIGINT / BIGSERIAL
UUID
VARCHAR
TEXT
DATE
TIMESTAMPTZ
BOOLEAN
INTEGER
NUMERIC(12,2)
JSONB
```

For primary keys, I recommend:

```text
BIGINT GENERATED ALWAYS AS IDENTITY
```

or UUID where public identifiers are exposed.

---

# 52. Recommended ID Strategy

Internally:

```text
BIGINT
```

Externally:

```text
patient_number
appointment_number
invoice_number
```

Example:

```text
PAT-2026-000001
APT-2026-000001
INV-2026-000001
```

Do not use the formatted number as your primary key.

Use:

```text
id BIGINT PRIMARY KEY
patient_number VARCHAR UNIQUE
```

---

# 53. Core Database Model

## Core tables

```text
core.patients
core.patient_contacts
core.patient_addresses
core.patient_relationships
core.patient_emergency_contacts
core.patient_identifiers
core.patient_consents
```

`core.patient_identifiers` (undefined elsewhere in this document — filling that gap here since it matters in the Philippine context):

```text
id
patient_id
identifier_type      -- e.g. philhealth, national_id, senior_citizen, pwd
identifier_value
issuing_authority
issued_at
expires_at
created_at
updated_at
```

`core.patient_consents` vs `documents.consent_documents` (§58/§79) are two different things and should reference each other, not duplicate each other:

- `patient_consents` is the **structured record**: consent type, `granted_at`, `revoked_at`, who obtained it — this is what your application logic actually checks before, say, sharing records or performing a procedure.
- `consent_documents` is the **evidence**: the scanned/signed PDF or photo of the signed form.

Give `patient_consents` an optional `consent_document_id` FK pointing to the file that backs it up, rather than treating the two as independent tables.

---

# 54. Clinical Tables

```text
clinical.medical_histories
clinical.medical_conditions
clinical.patient_conditions
clinical.allergens
clinical.patient_allergies
clinical.medications
clinical.patient_medications
clinical.dental_histories

clinical.encounters
clinical.clinical_notes
clinical.diagnoses
clinical.encounter_diagnoses

clinical.teeth
clinical.tooth_surfaces
clinical.odontograms
clinical.odontogram_entries
clinical.tooth_conditions

clinical.procedures
clinical.procedure_categories
clinical.procedure_records
clinical.procedure_surfaces

clinical.treatment_plans
clinical.treatment_plan_items

clinical.perio_examinations
clinical.perio_measurements

clinical.prescriptions
clinical.prescription_items

clinical.referrals
clinical.referral_documents
```

---

# 55. Scheduling Tables

```text
scheduling.appointment_types
scheduling.appointments
scheduling.appointment_statuses
scheduling.chairs
scheduling.providers
scheduling.provider_schedules
scheduling.queue_entries
scheduling.recall_types
scheduling.recalls
```

---

# 56. Billing Tables

```text
billing.invoices
billing.invoice_items
billing.payments
billing.payment_methods
billing.adjustments
billing.refunds
billing.discounts
billing.patient_ledger
```

---

# 57. Inventory Tables

```text
inventory.categories
inventory.products
inventory.units
inventory.suppliers
inventory.product_batches
inventory.stock_movements
inventory.stock_adjustments
inventory.purchase_orders
inventory.purchase_order_items
inventory.goods_receipts
inventory.goods_receipt_items
```

---

# 58. Documents Tables

```text
documents.file_types
documents.files
documents.patient_documents
documents.encounter_documents
documents.consent_documents
```

---

# 59. System Tables

```text
system.settings
system.audit_logs
system.activity_logs
system.number_sequences

-- future
system.users
system.roles
system.permissions
system.user_roles
system.role_permissions
```

---

# 60. Core Entity Relationship

```text
patients
   │
   ├────────────── patient_addresses
   │
   ├────────────── patient_contacts
   │
   ├────────────── patient_relationships
   │
   ├────────────── medical_histories
   │
   ├────────────── patient_allergies
   │
   ├────────────── dental_histories
   │
   ├────────────── appointments
   │                    │
   │                    └──── encounters
   │                              │
   │                              ├──── clinical_notes
   │                              ├──── diagnoses
   │                              ├──── procedure_records
   │                              ├──── prescriptions
   │                              ├──── perio_examinations
   │                              └──── documents
   │
   ├────────────── odontograms
   │                    │
   │                    └──── odontogram_entries
   │
   ├────────────── treatment_plans
   │                    │
   │                    └──── treatment_plan_items
   │
   └────────────── invoices
                        │
                        ├──── invoice_items
                        └──── payments
```

---

# 61. Suggested Core Table Structures

## patients

```text
id
patient_number
first_name
middle_name
last_name
suffix
preferred_name
date_of_birth
sex
civil_status
occupation
email
registration_date
referral_source
status
created_at
updated_at
archived_at
```

`registration_date` is kept as an explicit column even though it's close to `created_at`, because clinics migrating from paper records need to record a real historical registration date that predates when the row was inserted into the system — `created_at` should stay a pure "row was created" audit timestamp, never backdated.

`guardian` (from the Module 02 field list) is intentionally **not** a column here — it's handled by `patient_relationships` (§64) with `is_guardian = true`, since a patient can have more than one guardian and a guardian record needs its own fields (relationship type, contact info).

Do not put:

```text
age
```

in the database.

Calculate age from:

```text
date_of_birth
```

---

# 62. patient_addresses

```text
id
patient_id
address_type
address_line_1
address_line_2
barangay
city
province
postal_code
country
is_primary
created_at
updated_at
```

---

# 63. patient_contacts

```text
id
patient_id
contact_type
contact_value
is_primary
verified_at
created_at
updated_at
```

This supports:

```text
mobile
telephone
email
```

without adding endless columns.

---

# 64. patient_relationships

```text
id
patient_id
related_patient_id      -- nullable
contact_name             -- nullable
contact_phone            -- nullable
relationship_type
is_guardian
is_emergency_contact
created_at
updated_at
```

Most guardians and emergency contacts are **not** themselves patients (a parent who's never been treated, a spouse, an officemate). Don't assume `related_patient_id` is always populated:

- If the related person **is** an existing patient: set `related_patient_id`, leave `contact_name`/`contact_phone` null.
- If they are **not** a patient: leave `related_patient_id` null, populate `contact_name`/`contact_phone` directly.

Add a `CHECK` constraint requiring exactly one of `related_patient_id` or `contact_name` to be set.

Example (patient case):

```text
patient_id = 100
related_patient_id = 200
relationship_type = mother
```

Example (non-patient case):

```text
patient_id = 100
related_patient_id = NULL
contact_name = "Juan Dela Cruz"
contact_phone = "0917xxxxxxx"
relationship_type = father
is_emergency_contact = true
```

---

# 65. encounters

```text
id
encounter_number
patient_id
appointment_id
provider_id
encounter_type
status
started_at
ended_at
chief_complaint
clinical_summary
created_at
updated_at
```

---

# 66. clinical_notes

```text
id
encounter_id
note_type
note_text
status
created_by
signed_by
signed_at
created_at
updated_at
```

Recommended statuses:

```text
draft
signed
amended
```

---

# 67. procedures

```text
id
code
name
description
category_id
default_fee
default_duration_minutes
is_active
created_at
updated_at
```

---

# 68. procedure_records

```text
id
encounter_id
procedure_id
patient_id
provider_id
tooth_id
status
quantity
unit_price
total_amount
performed_at
notes
created_at
updated_at
```

---

# 69. treatment_plans

```text
id
patient_id
provider_id
plan_number
title
status
presented_at
accepted_at
completed_at
notes
created_at
updated_at
```

Use `provider_id` here, not `dentist_id` — it stays consistent with `encounters.provider_id`, `procedure_records.provider_id`, and `appointments.provider_id`, all of which reference `scheduling.providers`. If treatment plans should only ever be authored by a dentist (not a hygienist or assistant), enforce that with application logic or a `CHECK`/filtered FK against providers with a `dentist` role — don't do it by giving the column a different name.

---

# 70. treatment_plan_items

```text
id
treatment_plan_id
procedure_id
tooth_id
status
quantity
estimated_unit_price
estimated_total
priority
notes
created_at
updated_at
```

---

# 71. teeth

```text
id
notation_system
tooth_code
tooth_name
dentition_type
arch
position
is_active
```

Example:

```text
notation_system = FDI
tooth_code = 36
tooth_name = First molar
dentition_type = permanent
arch = mandibular
```

---

# 72. odontograms

```text
id
patient_id
encounter_id
dentition_type
notation_system
recorded_at
recorded_by
created_at
```

---

# 73. odontogram_entries

```text
id
odontogram_id
tooth_id
condition_id
status
notes
created_at
```

For surface-specific conditions:

```text
odontogram_entry_surfaces
```

---

# 74. tooth_conditions

```text
id
code
name
category
description
is_active
```

Examples:

```text
CARIES
MISSING
RESTORED
CROWN
BRIDGE
IMPLANT
RCT
FRACTURE
```

---

# 75. appointments

```text
id
appointment_number
patient_id
provider_id
appointment_type_id
chair_id
scheduled_start
scheduled_end
status
reason
notes
created_at
updated_at
cancelled_at
```

---

# 76. invoices

```text
id
invoice_number
patient_id
encounter_id
invoice_date
due_date
status
subtotal
discount_amount
tax_amount
total_amount
amount_paid
balance
created_at
updated_at
```

`balance` is kept as a stored column here for read performance (patient profile, dashboard, ledger screens all need it fast without recomputation), but it must never be written directly by application code. Maintain it as a **trigger-maintained cache**:

```text
balance = total_amount - SUM(payments) - SUM(adjustments)
```

recalculated by an `AFTER INSERT/UPDATE/DELETE` trigger on `payments` and `adjustments` (or in the same transaction that writes them). Application code only ever reads `balance`, never sets it — the same rule that applies to `invoices.balance` should apply to any other derived/cached total you store (e.g. `patient_ledger` running balances).

---

# 77. invoice_items

```text
id
invoice_id
procedure_id
treatment_plan_item_id
description
quantity
unit_price
discount_amount
total_amount
created_at
```

---

# 78. payments

```text
id
payment_number
patient_id
invoice_id               -- nullable; convenience FK for the simple single-invoice case
payment_date
payment_method_id
amount
reference_number
status
received_by
notes
created_at
updated_at
```

A payment is not always for exactly one invoice — a patient may pay a lump sum that covers several outstanding invoices, or pay before an invoice even exists (applied against the ledger). Don't model `invoice_id` as the only way a payment connects to what it pays for. Add:

```text
payment_allocations
--------------------
id
payment_id
invoice_id
amount_applied
created_at
```

`SUM(payment_allocations.amount_applied)` for a payment must always equal `payments.amount`. For the common single-invoice case, populate both `payments.invoice_id` (for fast/simple queries) and a matching `payment_allocations` row (for the general case) — application code reconciles the two, or `invoice_id` is dropped in favor of always going through allocations once this gets built.

---

# 79. Inventory

## products

```text
id
sku
name
description
category_id
unit_id
reorder_level
is_active
created_at
updated_at
```

## product_batches

```text
id
product_id
supplier_id
batch_number
lot_number
expiry_date
quantity
unit_cost
received_at
```

## stock_movements

```text
id
product_id
batch_id
movement_type
quantity
reference_type
reference_id
movement_date
performed_by
notes
```

---

# 80. Database Normalization

Target:

## 1NF

Every field should contain one value.

Bad:

```text
allergies = "Penicillin, Latex, Seafood"
```

Better:

```text
patient_allergies
```

---

## 2NF

Every non-key attribute should depend on the complete primary key.

Avoid redundant many-to-many data.

---

## 3NF

Non-key fields should depend on the key and not another non-key field.

Bad:

```text
patients
patient_id
doctor_id
doctor_name
```

Better:

```text
patients
doctor/provider reference
```

and:

```text
providers
provider_id
provider_name
```

---

# 81. Avoid Excessive JSONB

PostgreSQL `JSONB` is useful, but do not use it as an excuse to avoid database design.

Good use:

```text
custom_form_data JSONB
metadata JSONB
external_payload JSONB
```

Bad:

```text
patient_record JSONB
```

containing the entire clinical record.

Core clinical information should remain relational and queryable.

---

# 82. Foreign Keys

Every relationship should use foreign keys.

Example:

```text
appointments.patient_id
    REFERENCES patients(id)
```

This prevents orphaned records.

PostgreSQL foreign keys enforce referential integrity between related tables.

---

# 83. Delete Strategy

For master data:

```text
soft delete / deactivate
```

For clinical data:

```text
DO NOT DELETE
```

For example:

```text
clinical_notes
procedure_records
encounters
payments
```

should generally be immutable/history-preserving.

Instead use:

```text
status
voided_at
voided_by
amendment_reason
```

---

# 84. Database Constraints

Implement:

```text
PRIMARY KEY
FOREIGN KEY
UNIQUE
NOT NULL
CHECK
```

Examples:

```text
quantity > 0

amount >= 0

scheduled_end > scheduled_start

date_of_birth <= CURRENT_DATE
```

PostgreSQL provides these constraint mechanisms specifically for protecting data integrity.

---

# 85. Important Unique Constraints

Examples:

```text
patients.patient_number UNIQUE

procedures.code UNIQUE

products.sku UNIQUE

payment_methods.name UNIQUE

appointment_types.name UNIQUE
```

For patient information, don't blindly make mobile number or name unique because different patients can legitimately share them.

---

# 86. Indexing Strategy

Create indexes on:

```text
patients.patient_number
patients.last_name
patients.first_name
patients.date_of_birth

appointments.patient_id
appointments.provider_id
appointments.scheduled_start
appointments.status

encounters.patient_id
encounters.started_at

procedure_records.patient_id
procedure_records.tooth_id

treatment_plans.patient_id
treatment_plan_items.treatment_plan_id

invoices.patient_id
invoices.status
payments.invoice_id

stock_movements.product_id
product_batches.expiry_date
```

Note: earlier drafts referenced `encounters.encounter_date`, but the `encounters` table (§65) only has `started_at`/`ended_at` — index `started_at` directly, or add a generated column (`encounter_date DATE GENERATED ALWAYS AS (started_at::date) STORED`) if you need fast whole-day grouping.

For searching names:

Consider PostgreSQL:

```text
pg_trgm
```

for fast fuzzy/name searching.

---

# 87. Transactions

Critical operations must be transactional.

Example:

```text
Complete treatment
      ↓
Create procedure record
      ↓
Create invoice
      ↓
Create invoice item
      ↓
Update payment/ledger
      ↓
Commit
```

If something fails:

```text
ROLLBACK
```

Do not allow half-completed clinical/financial transactions.

---

# 88. Appointment Conflict Prevention

The database should help prevent:

```text
Dentist double-booking
Chair double-booking
```

PostgreSQL exclusion constraints can be useful for time-range conflicts. Concretely (requires the `btree_gist` extension for equality + range overlap in the same constraint):

```sql
CREATE EXTENSION IF NOT EXISTS btree_gist;

ALTER TABLE scheduling.appointments
  ADD CONSTRAINT no_provider_double_booking
  EXCLUDE USING gist (
    provider_id WITH =,
    tsrange(scheduled_start, scheduled_end) WITH &&
  ) WHERE (status NOT IN ('cancelled', 'no_show'));

ALTER TABLE scheduling.appointments
  ADD CONSTRAINT no_chair_double_booking
  EXCLUDE USING gist (
    chair_id WITH =,
    tsrange(scheduled_start, scheduled_end) WITH &&
  ) WHERE (status NOT IN ('cancelled', 'no_show'));
```

The `WHERE` clause matters — without it, cancelled/no-show appointments would still block the same slot from being rebooked.

This is preferable to relying only on frontend validation.

---

# 89. Patient Timeline

Create a unified clinical timeline.

Example:

```text
2026-08-01
Patient registered

2026-08-03
Initial consultation

2026-08-03
Diagnosis added

2026-08-03
Treatment plan created

2026-08-05
Treatment accepted

2026-08-13
Filling performed

2026-08-13
Payment received

2026-08-13
Follow-up scheduled
```

This will become one of the most useful screens in the application.

---

# 90. Recommended Patient Profile UI

When opening a patient:

```text
┌─────────────────────────────────────────────┐
│ PATIENT PROFILE                             │
├─────────────────────────────────────────────┤
│ Patient: Juan Dela Cruz                     │
│ Patient No: PAT-2026-000001                 │
│ DOB: Jan 15, 1990                           │
│ Contact: 09XXXXXXXXX                        │
├─────────────────────────────────────────────┤
│ [Overview] [Dental Chart] [Visits]          │
│ [Treatment] [Perio] [Images] [Documents]    │
│ [Prescriptions] [Billing] [Timeline]        │
└─────────────────────────────────────────────┘
```

This should become the central workspace.

---

# 91. Patient Overview

Show:

```text
Patient Information
Medical Alerts
Allergies
Current Medications
Upcoming Appointment
Outstanding Balance
Active Treatment Plan
Last Visit
Next Recall
```

---

# 92. Dental Chart Screen

Suggested layout:

```text
                DENTAL CHART

       ┌────── Upper Teeth ──────┐

        18 17 16 15 14 13 12 11
        21 22 23 24 25 26 27 28

       └─────────────────────────┘

       ┌──── Lower Teeth ────────┐

        48 47 46 45 44 43 42 41
        31 32 33 34 35 36 37 38

       └─────────────────────────┘

[Condition] [Restoration] [Missing] [Caries]
[Extraction] [RCT] [Crown] [Implant]
```

Clicking a tooth opens:

```text
Tooth Information
Current Condition
Historical Conditions
Surfaces
Treatment History
Notes
```

---

# 93. Appointment Workflow

```text
Patient Registration
       ↓
Appointment
       ↓
Confirmation
       ↓
Check-in
       ↓
Queue
       ↓
Encounter
       ↓
Dental Examination
       ↓
Treatment Plan
       ↓
Treatment
       ↓
Billing
       ↓
Payment
       ↓
Follow-up / Recall
```

---

# 94. Treatment Workflow

```text
Examination
    ↓
Diagnosis
    ↓
Treatment Plan
    ↓
Patient Acceptance
    ↓
Appointment
    ↓
Procedure
    ↓
Clinical Documentation
    ↓
Invoice
    ↓
Payment
    ↓
Follow-up
```

---

# 95. Inventory Workflow

```text
Supplier
   ↓
Purchase Order
   ↓
Goods Received
   ↓
Batch Created
   ↓
Stock Increased
   ↓
Dental Material Used
   ↓
Stock Movement
   ↓
Stock Decreased
```

---

# 96. MVP Scope

Do NOT build all 37 modules at once.

Build the first version around the clinical workflow.

## MVP Phase 1

- [ ] **Basic authentication (single login, no granular roles yet)**
- [ ] Dashboard
- [ ] Patient Registration
- [ ] Patient Profile
- [ ] Medical History
- [ ] Dental History
- [ ] Allergies
- [ ] Appointment Management
- [ ] Queue
- [ ] Encounter
- [ ] Clinical Notes
- [ ] Dental Chart
- [ ] Tooth Conditions
- [ ] Procedures
- [ ] Treatment Plans
- [ ] Treatment Records
- [ ] Billing
- [ ] Payments
- [ ] Patient Timeline
- [ ] Basic Reports
- [ ] Audit Trail (`actor_id` now points at a real logged-in user, not `NULL`)

Basic auth is pulled forward from Phase 3 into Phase 1. Granular RBAC (roles, permissions) can still wait for Phase 3 — but Phase 1 already handles real clinical notes and billing data, and "no login" plus "real patient PHI" living in the same phase is how a prototype quietly ends up in production unprotected. Hard rule: **this system does not touch real patient data until at least single-login authentication is in place.** Build and demo Phase 1 against synthetic/test data if authentication isn't ready yet.

---

# 97. Phase 2

- [ ] Periodontal Charting
- [ ] Prescription
- [ ] Imaging
- [ ] Patient Documents
- [ ] Consent Management
- [ ] Recall
- [ ] Referral
- [ ] Laboratory
- [ ] Inventory
- [ ] Suppliers
- [ ] Purchase Orders
- [ ] Advanced reports

---

# 98. Phase 3

- [ ] Granular role-based access (roles/permissions beyond the single Phase 1 login)
- [ ] Permission management UI
- [ ] Multi-clinic
- [ ] SMS
- [ ] Email
- [ ] Online appointment
- [ ] Patient portal
- [ ] Digital signatures
- [ ] Advanced audit
- [ ] Backup management
- [ ] API
- [ ] External integrations

---

# 99. Development Checklist

## A. Requirements

- [ ] Identify clinic workflow
- [ ] Interview dentist
- [ ] Interview dental assistant
- [ ] Interview receptionist
- [ ] Interview cashier
- [ ] Identify number of dental chairs
- [ ] Identify number of dentists
- [ ] Identify services
- [ ] Identify payment methods
- [ ] Identify current paper forms
- [ ] Identify existing billing process
- [ ] Identify inventory process
- [ ] Identify laboratory workflow
- [ ] Identify imaging workflow
- [ ] Identify patient consent forms
- [ ] Identify reporting requirements

---

# 100. Database Design Checklist

- [ ] Define PostgreSQL database
- [ ] Define schemas
- [ ] Define entities
- [ ] Define relationships
- [ ] Define primary keys
- [ ] Define foreign keys
- [ ] Define unique constraints
- [ ] Define check constraints
- [ ] Define indexes
- [ ] Define cascading rules
- [ ] Define soft-delete strategy
- [ ] Define audit strategy
- [ ] Define numbering strategy
- [ ] Define transaction boundaries
- [ ] Define database roles
- [ ] Define backup strategy

---

# 101. Master Data Checklist

- [ ] Procedure categories
- [ ] Procedures
- [ ] Tooth conditions
- [ ] Tooth surfaces
- [ ] Diagnosis terminology
- [ ] Medication master
- [ ] Appointment types
- [ ] Appointment statuses
- [ ] Payment methods
- [ ] Discount types
- [ ] Inventory categories
- [ ] Inventory units
- [ ] Document types
- [ ] Consent types
- [ ] Recall types
- [ ] Referral types

---

# 102. Patient Module Checklist

- [ ] Create patient
- [ ] Search patient
- [ ] View patient
- [ ] Edit patient
- [ ] Archive patient
- [ ] Restore patient
- [ ] Duplicate detection
- [ ] Guardian
- [ ] Emergency contact
- [ ] Address
- [ ] Contact information
- [ ] Medical history
- [ ] Dental history
- [ ] Allergies
- [ ] Medications
- [ ] Patient timeline
- [ ] Patient documents

---

# 103. Appointment Checklist

- [ ] Calendar
- [ ] Create appointment
- [ ] Edit appointment
- [ ] Reschedule
- [ ] Cancel
- [ ] Confirm
- [ ] Check-in
- [ ] Queue
- [ ] Start treatment
- [ ] Complete
- [ ] No-show
- [ ] Dentist assignment
- [ ] Chair assignment
- [ ] Conflict detection
- [ ] Appointment history

---

# 104. Clinical Checklist

- [ ] Create encounter
- [ ] Chief complaint
- [ ] Medical history review
- [ ] Dental history review
- [ ] Allergy warning
- [ ] Clinical examination
- [ ] Diagnosis
- [ ] Odontogram
- [ ] Tooth conditions
- [ ] Tooth surfaces
- [ ] Clinical notes
- [ ] Treatment plan
- [ ] Consent
- [ ] Procedure
- [ ] Prescription
- [ ] Imaging
- [ ] Follow-up
- [ ] Clinical timeline

---

# 105. Dental Chart Checklist

- [ ] FDI notation
- [ ] Universal notation
- [ ] Primary teeth
- [ ] Permanent teeth
- [ ] Tooth status
- [ ] Tooth condition
- [ ] Tooth surface
- [ ] Caries
- [ ] Restoration
- [ ] Crown
- [ ] Bridge
- [ ] Implant
- [ ] Root canal
- [ ] Extraction
- [ ] Missing tooth
- [ ] Historical changes
- [ ] Chart snapshot
- [ ] Chart history

---

# 106. Treatment Planning Checklist

- [ ] Create treatment plan
- [ ] Add procedure
- [ ] Assign tooth
- [ ] Assign surface
- [ ] Add estimated fee
- [ ] Add discount
- [ ] Set priority
- [ ] Present plan
- [ ] Accept
- [ ] Partially accept
- [ ] Decline
- [ ] Convert to appointment
- [ ] Convert to procedure
- [ ] Track completion
- [ ] Track remaining treatment

---

# 107. Billing Checklist

- [ ] Create invoice
- [ ] Add invoice items
- [ ] Calculate subtotal
- [ ] Apply discount
- [ ] Calculate total
- [ ] Record payment
- [ ] Print receipt
- [ ] Track balance
- [ ] Refund
- [ ] Void
- [ ] Adjustment
- [ ] Patient ledger
- [ ] Daily sales
- [ ] Revenue report
- [ ] Outstanding balance

---

# 108. Inventory Checklist

- [ ] Product CRUD
- [ ] Category CRUD
- [ ] Unit CRUD
- [ ] Supplier CRUD
- [ ] Batch tracking
- [ ] Lot tracking
- [ ] Expiry tracking
- [ ] Stock-in
- [ ] Stock-out
- [ ] Adjustment
- [ ] Purchase order
- [ ] Goods receipt
- [ ] Low-stock alert
- [ ] Expiry report
- [ ] Stock movement report

---

# 109. Document Checklist

- [ ] Upload document
- [ ] View document
- [ ] Download
- [ ] Delete/archive
- [ ] Document type
- [ ] Patient association
- [ ] Encounter association
- [ ] File size validation
- [ ] MIME validation
- [ ] Virus/malware scanning
- [ ] Secure storage
- [ ] Access logging

---

# 110. Security Checklist

## Before Production

- [ ] Authentication implemented
- [ ] Password hashing
- [ ] Role-based access
- [ ] Permission checks
- [ ] HTTPS
- [ ] Secure cookies
- [ ] CSRF protection
- [ ] XSS protection
- [ ] SQL injection protection
- [ ] Rate limiting
- [ ] Secure headers
- [ ] Input validation
- [ ] File upload security
- [ ] Database least privilege
- [ ] Database encryption strategy
- [ ] Backup encryption
- [ ] Audit logging
- [ ] Access logging
- [ ] Error logging
- [ ] Security monitoring
- [ ] Backup verification
- [ ] Restore testing
- [ ] Incident response procedure
- [ ] Data retention policy
- [ ] Data disposal procedure
- [ ] Privacy notice
- [ ] Consent mechanisms where required
- [ ] Data subject request workflow

The NPC's security guidance specifically discusses encryption of transmitted personal information, system/network protection, monitoring, patching, malware protection, and employee security training.

---

# 111. Data Privacy Checklist — Philippines

Because this is intended for a Philippine dental clinic, treat privacy as a first-class system requirement.

The Philippine Data Privacy Act covers the collection, recording, storage, modification, retrieval, use, disclosure, blocking, erasure and destruction of personal information. Health information is treated as sensitive personal information under the implementing rules.

Implement:

- [ ] Privacy notice
- [ ] Defined processing purposes
- [ ] Data minimization
- [ ] Appropriate consent mechanisms
- [ ] Access controls
- [ ] Audit trail
- [ ] Secure backups
- [ ] Data retention policy
- [ ] Secure deletion
- [ ] Breach response process
- [ ] Data export process
- [ ] Patient record access process
- [ ] Data correction process
- [ ] Third-party processor controls
- [ ] Confidentiality obligations
- [ ] Security policies
- [ ] Regular security assessment

Do not simply copy HIPAA requirements and call the application compliant. For a Philippine clinic, the **Data Privacy Act of 2012 and NPC requirements/guidance** need to be considered, alongside applicable dental/health regulations and professional advice.

---

# 112. Audit Checklist

Every sensitive operation should potentially produce:

```text
audit_log
```

Fields:

```text
id
actor_id
action
entity_type
entity_id
old_values
new_values
ip_address
user_agent
created_at
```

Before basic authentication lands (early scaffolding/local dev only, never with real patient data — see §96):

```text
actor_id = NULL
```

can temporarily be allowed.

From MVP Phase 1 onward, once single-login auth is in place:

```text
actor_id → users.id
```

should always be populated — this is also the point at which `actor_id` stops being nullable.

---

# 113. Testing Checklist

## Database

- [ ] PK tests
- [ ] FK tests
- [ ] Unique constraint tests
- [ ] Check constraint tests
- [ ] Cascade tests
- [ ] Transaction tests
- [ ] Concurrent update tests

## Patient

- [ ] Create
- [ ] Search
- [ ] Update
- [ ] Archive
- [ ] Duplicate detection

## Appointment

- [ ] Create
- [ ] Reschedule
- [ ] Cancel
- [ ] No-show
- [ ] Dentist conflict
- [ ] Chair conflict

## Dental Chart

- [ ] Tooth selection
- [ ] Condition creation
- [ ] Surface selection
- [ ] History
- [ ] Permanent dentition
- [ ] Primary dentition

## Treatment

- [ ] Plan creation
- [ ] Acceptance
- [ ] Procedure completion
- [ ] Billing conversion

## Billing

- [ ] Invoice
- [ ] Payment
- [ ] Partial payment
- [ ] Refund
- [ ] Adjustment
- [ ] Balance calculation

---

# 114. Recommended Development Order

Do it in this order:

```text
1. Database foundation
       ↓
2. Master data
       ↓
3. Patient management
       ↓
4. Appointment
       ↓
5. Encounter
       ↓
6. Dental chart
       ↓
7. Treatment planning
       ↓
8. Procedures
       ↓
9. Billing
       ↓
10. Payments
       ↓
11. Patient timeline
       ↓
12. Reports
       ↓
13. Audit
       ↓
14. Documents
       ↓
15. Perio
       ↓
16. Inventory
       ↓
17. Authentication
       ↓
18. RBAC
       ↓
19. Notifications
       ↓
20. Integrations
```

---

# 115. Most Important Design Decision

The most important distinction in this application is:

```text
MASTER DATA
       ↓
TRANSACTION
       ↓
HISTORY
```

For example:

```text
procedures
```

is master data.

```text
procedure_records
```

is the actual treatment.

```text
clinical_notes / audit_logs
```

provide historical documentation.

Similarly:

```text
products
```

is master data.

```text
stock_movements
```

are transactions.

```text
invoices
```

are financial transactions.

This separation is what will keep the database clean as the application becomes larger.

---

# 116. Recommended Final Module Structure

```text
DENTAL CLINIC INFORMATION MANAGEMENT SYSTEM
│
├── Dashboard
│
├── PATIENT MANAGEMENT
│   ├── Patients
│   ├── Guardians
│   ├── Emergency Contacts
│   ├── Medical History
│   ├── Dental History
│   ├── Allergies
│   ├── Medications
│   └── Documents
│
├── APPOINTMENTS
│   ├── Calendar
│   ├── Appointments
│   ├── Queue
│   ├── Chairs
│   └── Recall
│
├── CLINICAL
│   ├── Encounters
│   ├── Clinical Notes
│   ├── Diagnosis
│   ├── Dental Chart
│   ├── Tooth Conditions
│   ├── Treatment Plans
│   ├── Procedures
│   ├── Periodontal Chart
│   ├── Prescriptions
│   ├── Imaging
│   ├── Consent
│   └── Referrals
│
├── LABORATORY
│   ├── Lab Cases
│   ├── Lab Orders
│   └── Lab Receipts
│
├── BILLING
│   ├── Invoices
│   ├── Payments
│   ├── Discounts
│   ├── Adjustments
│   ├── Refunds
│   └── Patient Ledger
│
├── INVENTORY
│   ├── Products
│   ├── Categories
│   ├── Suppliers
│   ├── Batches
│   ├── Stock Movement
│   ├── Purchase Orders
│   └── Goods Receipts
│
├── REPORTS
│   ├── Patient Reports
│   ├── Appointment Reports
│   ├── Clinical Reports
│   ├── Treatment Reports
│   ├── Financial Reports
│   └── Inventory Reports
│
├── ADMINISTRATION
│   ├── Settings
│   ├── Master Data
│   ├── Audit Logs
│   └── Activity Logs
│
└── SECURITY
    ├── Users
    ├── Roles
    ├── Permissions
    ├── Sessions
    └── Security Logs
```

---

# 117. Recommended MVP Database Relationship

The minimum database should revolve around this:

```text
                         ┌──────────────┐
                         │   PATIENTS   │
                         └──────┬───────┘
                                │
          ┌─────────────────────┼─────────────────────┐
          │                     │                     │
          ▼                     ▼                     ▼
   APPOINTMENTS            MEDICAL HISTORY       DENTAL HISTORY
          │
          ▼
      ENCOUNTERS
          │
    ┌─────┼───────────┬───────────────┐
    │     │           │               │
    ▼     ▼           ▼               ▼
 NOTES  DIAGNOSIS  PROCEDURES     ODONTOGRAM
    │                 │               │
    │                 ▼               ▼
    │          TREATMENT PLAN    TOOTH RECORDS
    │
    ├──────── PRESCRIPTIONS
    │
    ├──────── IMAGING
    │
    └──────── CONSENTS

              PATIENT
                 │
                 ▼
             INVOICES
                 │
          ┌──────┴──────┐
          ▼             ▼
    INVOICE ITEMS     PAYMENTS
```

This is the core of the system.

---

# 118. Final Recommendation

For your project, I would build it as a **Dental Practice Management + Dental EHR hybrid**, rather than a generic CIMS.

The strongest architecture is:

```text
                 DENTAL CLINIC
                      │
        ┌─────────────┴─────────────┐
        │                           │
   ADMINISTRATIVE                CLINICAL
        │                           │
 Patient / Appointment        Encounter
 Billing                      Dental Chart
 Inventory                    Diagnosis
 Reports                      Treatment
                              Perio
                              Prescription
                              Imaging
        │                           │
        └─────────────┬─────────────┘
                      │
                  POSTGRESQL
                      │
             Audit + Security
```

The **three most important database concepts** are:

1. **Patient ≠ Encounter ≠ Treatment**
2. **Treatment Plan ≠ Completed Procedure**
3. **Clinical Record ≠ Financial Record**

If you get those three right, the rest of the architecture becomes much easier to scale.

Also, don't make the odontogram a collection of hard-coded columns. Make **teeth, surfaces, conditions, and chart entries relational entities**. That gives you a proper historical dental record and makes future FDI/Universal notation, periodontal charting, reporting, and analytics much easier.

For production, treat the patient/clinical database as a sensitive-data system from day one. Concretely, that means basic authentication ships with MVP Phase 1 (§96), not Phase 3 — early scaffolding without login is fine for local development, but nothing with real patient data should run unauthenticated. The Philippine privacy framework requires appropriate safeguards, and health information receives sensitive-data treatment.

**Best next technical step:** build the PostgreSQL schema/ERD first, before building the UI. The next deliverable should be a complete **PostgreSQL DDL (`CREATE TABLE`) for all MVP tables, including PKs, FKs, indexes, enums/check constraints, timestamps, soft-delete fields, audit structure, and seed/master data**, followed by the API/service layer and then the UI.