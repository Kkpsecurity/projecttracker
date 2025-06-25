# Database Structure Report

## Tables

### hb837
- **id** (bigint, primary key)
- **user_id** (bigint, nullable, foreign key → users.id)
- **client_id** (bigint, nullable, foreign key → clients.id)
- **assigned_consultant_id** (bigint, nullable, foreign key → consultants.id)
- **owner_id** (bigint, nullable, foreign key → owners.id, deprecated)
- **owner_name** (string, nullable)
- **management_company** (string, nullable)
- **property_name** (string, nullable)
- **property_type** (enum, nullable, values from config)
- **units** (integer, nullable)
- **address** (string, nullable)
- **city** (string, nullable)
- **county** (string, nullable)
- **state** (string, 2, nullable)
- **zip** (string, 10, nullable)
- **phone** (string, 15, nullable)
- **scheduled_date_of_inspection** (date, nullable)
- **report_submitted** (date, nullable)
- **report_status** (enum, nullable, values from config)
- **securitygauge_crime_risk** (enum, nullable, values from config)
- **property_manager_name** (string, nullable)
- **property_manager_email** (string, nullable)
- **regional_manager_name** (string, nullable)
- **regional_manager_email** (string, nullable)
- **agreement_submitted** (date, nullable)
- **contracting_status** (enum, nullable, values from config)
- **quoted_price** (float(11,2), nullable)
- **sub_fees_estimated_expenses** (float(11,2), nullable)
- **project_net_profit** (float(11,2), nullable)
- **billing_req_sent** (date, nullable)
- **financial_notes** (text, nullable)
- **macro_client** (string, nullable)
- **macro_contact** (string, nullable)
- **macro_email** (string, nullable)
- **notes** (text, nullable)
- **consultant_notes** (text, nullable, via migration)
- **created_at** (timestamp)
- **updated_at** (timestamp)

#### Foreign Keys
- `user_id` → users.id (set null on delete)
- `client_id` → clients.id (set null on delete)
- `assigned_consultant_id` → consultants.id (set null on delete)
- `owner_id` → owners.id (set null on delete)

---

### hb837_files
- **id** (bigint, primary key)
- **user_id** (bigint, nullable, foreign key → users.id)
- **hb837_id** (bigint, foreign key → hb837.id)
- **filename** (string)
- **file_type** (string)
- **original_filename** (string)
- **file_path** (string)
- **file_size** (integer, nullable)
- **created_at** (timestamp)
- **updated_at** (timestamp)

#### Foreign Keys
- `user_id` → users.id (set null on delete)
- `hb837_id` → hb837.id (cascade on delete)

---

## Notes
- Some enum values are loaded from Laravel config files.
- The `owner_id` field in `hb837` is deprecated.
- Timestamps are managed by Laravel.

---

This report is based on the migration files as of June 24, 2025.
