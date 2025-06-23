Here’s a documentation of your database structure and relationships so far based on the provided models and migrations.

---

# **Database Documentation**

## **Tables**

### 1. `hb837` (Projects)
- **Purpose**: Stores details about HB837 projects, including relationships with owners, consultants, and management companies.
- **Fields**:
  - `id`: Primary key.
  - `user_id`: Foreign key linking to `users` table (nullable).
  - `client_id`: Foreign key linking to `clients` table (nullable).
  - `management_company_id`: Foreign key linking to `management_companies` table (nullable).
  - `owner_id`: Foreign key linking to `owners` table (nullable).
  - `assigned_consultant_id`: Foreign key linking to `consultants` table (nullable).
  - `report_status`: Status of the report (default: `Not Started`).
  - `property_name`: Name of the property.
  - `property_type`: Type of the property (e.g., Garden, Mid-Rise, High Rise).
  - `units`: Number of units in the property.
  - `address`, `city`, `county`, `state`, `zip`: Property location details.
  - **Inspection Details**:
    - `scheduled_date_of_inspection`
    - `report_submitted`
    - `billing_req_sent`
  - **Contact Details**:
    - `phone`, `property_manager_name`, `property_manager_email`
    - `regional_manager_name`, `regional_manager_email`
  - **Contracting Details**:
    - `agreement_submitted`
    - `contracting_status` (e.g., `Quoted`, `Started`, `Executed`)
  - **Financial Data**:
    - `quoted_price`
    - `sub_fees_estimated_expenses`
    - `project_net_profit`

### 2. `management_companies`
- **Purpose**: Stores information about management companies involved in HB837 projects.
- **Fields**:
  - `id`: Primary key.
  - `name`: Name of the company.
  - `email`: Email address.
  - `phone`: Phone number.
  - `website`: Website URL.
  - `address`, `city`, `state`, `zip`: Address details.
  - `contact_person_name`: Name of the primary contact.
  - `contact_person_email`: Email of the primary contact.

### 3. `owners`
- **Purpose**: Stores details about property owners.
- **Fields**:
  - `id`: Primary key.
  - `name`: Name of the owner.
  - `email`: Email address.
  - `phone`: Phone number.
  - `address`, `city`, `state`, `zip`: Address details.
  - `company_name`: Name of the company (if applicable).
  - `tax_id`: Tax Identification Number.

### 4. `consultants`
- **Purpose**: Stores information about consultants assigned to HB837 projects.
- **Fields**:
  - `id`: Primary key.
  - `first_name`, `last_name`: Consultant's name.
  - `email`: Email address (unique).
  - `phone`: Phone number.
  - `specialization`: Consultant's area of expertise.
  - `status`: Current status (default: `Active`).
  - `address`, `city`, `state`, `zip`: Address details.

---

## **Relationships**

### 1. `hb837`
- **One-to-Many**:
  - `user_id` → `users(id)`
  - `client_id` → `clients(id)`
  - `management_company_id` → `management_companies(id)`
  - `owner_id` → `owners(id)`
  - `assigned_consultant_id` → `consultants(id)`

### 2. `management_companies`
- **One-to-Many**:
  - A management company can manage multiple projects (`hb837`).

### 3. `owners`
- **One-to-Many**:
  - An owner can own multiple properties (`hb837`).

### 4. `consultants`
- **One-to-Many**:
  - A consultant can be assigned to multiple projects (`hb837`).

---

## **ER Diagram**

The relationships described can be visualized as follows:

```
users (id) -----------------< hb837 (user_id)
clients (id) ---------------< hb837 (client_id)
management_companies (id) --< hb837 (management_company_id)
owners (id) ----------------< hb837 (owner_id)
consultants (id) -----------< hb837 (assigned_consultant_id)
```

---

## **Future Improvements**
- **Indexes**: Add indexes to commonly queried fields like `report_status`, `contracting_status`, and `assigned_consultant_id`.
- **Constraints**: Consider adding constraints for enum values (`contracting_status`, `report_status`) to ensure data integrity.
- **Soft Deletes**: Enable soft deletes for critical tables like `hb837`, `management_companies`, `owners`, and `consultants`.

Let me know if you want to add anything or generate an actual ER diagram!