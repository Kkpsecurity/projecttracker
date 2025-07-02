# HB837 Import System - Field Mapping Configuration

## Overview
We have created a comprehensive import system for HB837 project data with a complete field mapping configuration that handles all possible Excel column variations.

## Import Rules Implementation

### Core Rules
1. **If property/address doesn't exist** → **CREATE** new record
2. **If property/address exists** → **CHECK** what fields changed and **UPDATE**
3. **If field in DB is empty** → **UPDATE** with new value  
4. **If field in DB has value and import has different value** → **UPDATE** (with detailed logging)
5. **Map ALL fields** from Excel files completely

## Files Created/Updated

### 1. Configuration File: `config/hb837_field_mapping.php`
- **34 database fields** mapped to **160+ possible Excel column names**
- Import rules and default values
- Validation rules for each field type
- Data transformation rules
- Status normalization maps

### 2. Enhanced Import Class: `app/Imports/EnhancedHB837Import.php`
- Uses configuration-based field mapping
- Implements all import rules
- Comprehensive logging for all operations
- Handles data validation and transformation
- Tracks field-level changes

### 3. Test Script: `test_field_mapping_config.php`
- Verifies configuration loading
- Tests import class instantiation
- Shows field mapping summary

## Complete Field Coverage

### Core Identifiers
- **property_name** (6 variations): Property Name, PropertyName, Name, Property, Building Name, Site Name
- **address** (6 variations): Address, Property Address, Street Address, Location, Site Address, Building Address

### Location Details
- **city** (3 variations): City, Municipality, Town
- **county** (3 variations): County, Parish, District  
- **state** (4 variations): State, ST, Province, Region
- **zip** (5 variations): Zip, ZIP, Zip Code, Postal Code, Post Code

### Property Details
- **property_type** (5 variations): Property Type, Type, Building Type, Structure Type, Asset Type
- **units** (6 variations): Units, Unit Count, Number of Units, # Units, Total Units, Apt Count
- **owner_name** (5 variations): Owner Name, Owner, Property Owner, Building Owner, Landlord

### Contact Information
- **phone** (6 variations): Phone, Phone Number, Contact Phone, Tel, Telephone, Primary Phone
- **management_company** (5 variations): Management Company, Manager, Property Manager Company, Property Management, Mgmt Company
- **property_manager_name** (5 variations): Property Manager Name, PM Name, Manager Name, Site Manager, On-Site Manager
- **property_manager_email** (5 variations): Property Manager Email, PM Email, Manager Email, Site Manager Email, Property Manager E-mail
- **regional_manager_name** (5 variations): Regional Manager Name, RM Name, Regional Manager, Area Manager, District Manager
- **regional_manager_email** (4 variations): Regional Manager Email, RM Email, Area Manager Email, District Manager Email

### Status Fields
- **report_status** (5 variations): Report Status, Status, Project Status, Inspection Status, Work Status
- **contracting_status** (5 variations): Contracting Status, Contract Status, Contract State, Agreement Status, Execution Status

### Financial Fields
- **quoted_price** (6 variations): Quoted Price, Quote, Price, Amount, Contract Amount, Project Cost
- **sub_fees_estimated_expenses** (6 variations): Sub Fees Estimated Expenses, Sub Fees, Expenses, Additional Fees, Extra Costs, Misc Expenses
- **project_net_profit** (5 variations): Project Net Profit, Net Profit, Profit, Margin, Net Margin
- **financial_notes** (5 variations): Financial Notes, Money Notes, Billing Notes, Cost Notes, Payment Notes

### Security & Risk
- **securitygauge_crime_risk** (6 variations): SecurityGauge Crime Risk, Crime Risk, Risk Level, Security Risk, Safety Risk, Crime Rating

### Consultant/Inspector
- **assigned_consultant_id** (4 variations): Assigned Consultant ID, Consultant ID, Inspector ID, Technician ID

### Date Fields
- **scheduled_date_of_inspection** (6 variations): Scheduled Date of Inspection, Inspection Date, Schedule Date, Date, Appointment Date, Visit Date
- **report_submitted** (5 variations): Report Submitted, Report Date, Submission Date, Report Completed, Report Delivery Date
- **agreement_submitted** (4 variations): Agreement Submitted, Agreement Date, Contract Date, Signed Date
- **billing_req_sent** (5 variations): Billing Req Sent, Billing Date, Invoice Date, Bill Sent, Payment Request Date

### Macro Client Information
- **macro_client** (5 variations): Macro Client, Client, Parent Company, Corporate Client, Main Client
- **macro_contact** (5 variations): Macro Contact, Client Contact, Main Contact, Primary Contact, Corporate Contact
- **macro_email** (5 variations): Macro Email, Client Email, Contact Email, Primary Email, Corporate Email

### Notes
- **notes** (6 variations): Notes, General Notes, Comments, Additional Info, Remarks, Description
- **private_notes** (4 variations): Private Notes, Internal Notes, Admin Notes, Confidential Notes

## Data Validation & Transformation

### Status Normalization
- **Report Status**: Maps variations like "not started", "in progress", "in review", "completed"
- **Contracting Status**: Maps variations like "quote", "start", "execute", "close"

### Data Type Handling
- **Date Fields**: Handles Excel date formats and text dates
- **Money Fields**: Strips currency symbols and formats as decimal
- **Integer Fields**: Validates and converts to integers
- **Text Fields**: Trims and sanitizes text input

### Field Validation
- **State**: Max 2 characters, uppercase
- **Zip**: Max 10 characters, numeric and dash only
- **Phone**: Max 15 characters, phone format
- **Property Type**: Limited to allowed values (garden, midrise, highrise, industrial, bungalo)

## Usage

### Testing Configuration
```bash
php test_field_mapping_config.php
```

### Using in Import Process
The `EnhancedHB837Import` class now automatically loads the configuration and applies all field mappings and validation rules.

## Benefits

1. **Complete Coverage**: All possible Excel column variations are handled
2. **Maintainable**: Configuration-based approach makes it easy to add new mappings
3. **Flexible**: Handles multiple naming conventions for the same field
4. **Robust**: Comprehensive validation and error handling
5. **Auditable**: Detailed logging of all changes and operations
6. **Extensible**: Easy to add new fields or mapping variations

This system ensures that any Excel file with HB837 project data will have its fields properly mapped and imported, regardless of the column naming conventions used.
