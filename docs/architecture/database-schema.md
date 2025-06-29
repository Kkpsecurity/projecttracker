# Database Schema Documentation

Generated on: 2025-06-29 11:00:42

## Overview

This document provides a comprehensive overview of the ProjectTracker database schema, including all tables, columns, relationships, and constraints.

## Table of Contents

- [Backups](#backups)
- [Clients](#clients)
- [Consultant files](#consultant-files)
- [Consultants](#consultants)
- [Failed jobs](#failed-jobs)
- [Hb837](#hb837)
- [Hb837 files](#hb837-files)
- [Import audits](#import-audits)
- [Migrations](#migrations)
- [Owners](#owners)
- [Password resets](#password-resets)
- [Plot addresses](#plot-addresses)
- [Plots](#plots)
- [Site settings](#site-settings)
- [Users](#users)

## Database Statistics

| Table | Row Count |
|-------|----------|
| `backups` | 0 |
| `clients` | 129 |
| `consultant_files` | 0 |
| `consultants` | 5 |
| `failed_jobs` | 0 |
| `hb837` | 5 |
| `hb837_files` | 0 |
| `import_audits` | 0 |
| `migrations` | 20 |
| `owners` | 0 |
| `password_resets` | 0 |
| `plot_addresses` | 22 |
| `plots` | 8 |
| `site_settings` | 0 |
| `users` | 7 |

## Entity Relationship Overview

The database follows a normalized structure with the following key relationships:

- **Users** can have administrative privileges and track login activity
- **Clients** represent companies with billing information and contact details
- **Consultants** are specialized staff members who handle HB837 applications
- **HB837** applications are linked to consultants and plots
- **Plots** have associated addresses and can be referenced by multiple applications
- **Import Audits** track data import operations across all tables
- **Backups** maintain records of database backup operations
- **Site Settings** store application configuration values

## Backups

**Table Name:** `backups`
**Row Count:** 0

**Purpose:** Records database backup operations and file locations

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `uuid` | char | Field description |
| `name` | varchar | Name field |
| `tables` | json | Field description |
| `user_id` | bigint | Field description |
| `filename` | varchar | Field description |
| `size` | bigint | Field description |
| `record_count` | int | Field description |
| `status` | varchar | Record status |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |

---

## Clients

**Table Name:** `clients`
**Row Count:** 129

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `client_name` | varchar | Field description |
| `project_name` | varchar | Field description |
| `poc` | text | Field description |
| `status` | text | Record status |
| `quick_status` | text | Field description |
| `description` | text | Human-readable setting description |
| `corporate_name` | text | Field description |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |
| `file1` | varchar | Field description |
| `file2` | varchar | Field description |
| `file3` | varchar | Field description |
| `project_services_total` | float | Field description |
| `project_expenses_total` | float | Field description |
| `final_services_total` | float | Field description |
| `final_billing_total` | float | Field description |

---

## Consultant files

**Table Name:** `consultant_files`
**Row Count:** 0

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `consultant_id` | bigint | Reference to consultant handling the case |
| `file_type` | varchar | Field description |
| `original_filename` | varchar | Field description |
| `file_path` | varchar | Field description |
| `file_size` | int | Field description |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |

---

## Consultants

**Table Name:** `consultants`
**Row Count:** 5

**Purpose:** Manages consultant profiles, specializations, and employment status

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `first_name` | varchar | Field description |
| `last_name` | varchar | Field description |
| `email` | varchar | Email address |
| `dba_company_name` | varchar | Field description |
| `mailing_address` | varchar | Field description |
| `fcp_expiration_date` | date | Field description |
| `assigned_light_meter` | varchar | Field description |
| `lm_nist_expiration_date` | date | Field description |
| `subcontractor_bonus_rate` | decimal | Field description |
| `notes` | text | Field description |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |

---

## Failed jobs

**Table Name:** `failed_jobs`
**Row Count:** 0

**Purpose:** Tracks failed background job executions for debugging

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `connection` | text | Field description |
| `queue` | text | Field description |
| `payload` | longtext | Field description |
| `exception` | longtext | Field description |
| `failed_at` | timestamp | Field description |

---

## Hb837

**Table Name:** `hb837`
**Row Count:** 5

**Purpose:** Tracks HB837 applications, their status, and consultant assignments

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `user_id` | bigint | Field description |
| `client_id` | bigint | Field description |
| `assigned_consultant_id` | bigint | Field description |
| `owner_name` | varchar | Field description |
| `management_company` | varchar | Field description |
| `property_name` | varchar | Field description |
| `property_type` | enum | Field description |
| `units` | int | Field description |
| `address` | varchar | Physical address |
| `city` | varchar | City location |
| `county` | varchar | Field description |
| `state` | varchar | State/province |
| `zip` | varchar | Field description |
| `phone` | varchar | Contact phone number |
| `scheduled_date_of_inspection` | date | Field description |
| `report_submitted` | date | Field description |
| `report_status` | enum | Field description |
| `securitygauge_crime_risk` | enum | Field description |
| `property_manager_name` | varchar | Field description |
| `property_manager_email` | varchar | Field description |
| `regional_manager_name` | varchar | Field description |
| `regional_manager_email` | varchar | Field description |
| `agreement_submitted` | date | Field description |
| `contracting_status` | enum | Field description |
| `quoted_price` | float | Field description |
| `sub_fees_estimated_expenses` | float | Field description |
| `project_net_profit` | float | Field description |
| `billing_req_sent` | date | Field description |
| `financial_notes` | text | Field description |
| `macro_client` | varchar | Field description |
| `macro_contact` | varchar | Field description |
| `macro_email` | varchar | Field description |
| `notes` | text | Field description |
| `consultant_notes` | text | Notes from assigned consultant |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |

---

## Hb837 files

**Table Name:** `hb837_files`
**Row Count:** 0

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `user_id` | bigint | Field description |
| `hb837_id` | bigint | Field description |
| `filename` | varchar | Field description |
| `file_type` | varchar | Field description |
| `original_filename` | varchar | Field description |
| `file_path` | varchar | Field description |
| `file_size` | int | Field description |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |

---

## Import audits

**Table Name:** `import_audits`
**Row Count:** 0

**Purpose:** Logs all data import operations and their results

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `import_id` | char | Field description |
| `type` | varchar | Field description |
| `changes` | json | Field description |
| `user_id` | bigint | Field description |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |

---

## Migrations

**Table Name:** `migrations`
**Row Count:** 20

**Purpose:** Laravel framework table tracking applied database migrations

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | int | Primary key identifier |
| `migration` | varchar | Field description |
| `batch` | int | Field description |

---

## Owners

**Table Name:** `owners`
**Row Count:** 0

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `name` | varchar | Name field |
| `email` | varchar | Email address |
| `phone` | varchar | Contact phone number |
| `address` | varchar | Physical address |
| `city` | varchar | City location |
| `state` | varchar | State/province |
| `zip` | varchar | Field description |
| `company_name` | varchar | Client company name |
| `tax_id` | varchar | Field description |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |

---

## Password resets

**Table Name:** `password_resets`
**Row Count:** 0

**Purpose:** Handles password reset tokens for user authentication

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `email` | varchar | Email address |
| `token` | varchar | Field description |
| `created_at` | timestamp | Record creation timestamp |

---

## Plot addresses

**Table Name:** `plot_addresses`
**Row Count:** 22

**Purpose:** Stores detailed address information for plots

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `plot_id` | bigint | Reference to associated plot |
| `latitude` | varchar | Field description |
| `longitude` | varchar | Field description |
| `location_name` | varchar | Field description |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |
| `status` | varchar | Record status |

---

## Plots

**Table Name:** `plots`
**Row Count:** 8

**Purpose:** Manages plot information and identifiers

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `plot_name` | varchar | Field description |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |
| `status` | varchar | Record status |
| `notes` | text | Field description |

---

## Site settings

**Table Name:** `site_settings`
**Row Count:** 0

**Purpose:** Stores application-wide configuration settings

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `key` | varchar | Setting identifier key |
| `value` | text | Setting value |
| `type` | varchar | Field description |
| `group` | varchar | Field description |
| `label` | varchar | Field description |
| `description` | text | Human-readable setting description |
| `is_public` | tinyint | Field description |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |

---

## Users

**Table Name:** `users`
**Row Count:** 7

**Purpose:** Manages user accounts, authentication, and administrative access

### Columns

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key identifier |
| `name` | varchar | Name field |
| `email` | varchar | Email address |
| `email_verified_at` | timestamp | Email verification timestamp |
| `password` | varchar | Encrypted password |
| `remember_token` | varchar | Remember me token for login persistence |
| `is_admin` | tinyint | Administrative privileges flag |
| `is_active` | tinyint | Active employment status |
| `last_login_at` | timestamp | Last login timestamp |
| `avatar` | varchar | Field description |
| `phone` | varchar | Contact phone number |
| `bio` | text | Field description |
| `preferences` | json | Field description |
| `email_verified` | tinyint | Field description |
| `two_factor_enabled` | tinyint | Field description |
| `password_changed_at` | timestamp | Field description |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record last update timestamp |

---

