# HB837 Smart Import User Guide

## Overview
The HB837 Smart Import system is designed to make importing property data as simple as possible for agents. Just upload your file, and the system handles everything automatically!

## How to Use Smart Import

### Step 1: Access Smart Import
1. Go to the HB837 Management dashboard
2. Click the **"Smart Import"** button (blue button with magic wand icon)

### Step 2: Upload Your File
**Option A: Drag & Drop**
- Simply drag your file from Windows Explorer directly into the upload area
- The system will automatically detect when you're dragging a file

**Option B: Click to Browse**
- Click the "Choose File" button
- Select your file from the file dialog

### Supported File Types
- **Excel Files**: `.xlsx`, `.xls` (Most common)
- **CSV Files**: `.csv` (Comma-separated values)
- **Email Files**: `.eml`, `.msg` (Outlook emails with attachments)

### Step 3: Automatic Analysis
Once you upload a file, the system automatically:

1. **Detects File Format**: Identifies whether it's Excel, CSV, or email
2. **Reads Data Structure**: Analyzes columns and data types
3. **Smart Column Mapping**: Matches your columns to HB837 fields
4. **Data Validation**: Checks for errors and duplicates
5. **Preview Generation**: Shows you what will be imported

### Step 4: Review and Import
- Review the analysis results and column mappings
- Check the data preview to ensure everything looks correct
- Click "Import Data" to complete the process

## What the System Can Handle

### Intelligent Column Detection
The system automatically recognizes common column names:
- Property names, addresses, locations
- Client information and contacts
- Dates (in various formats)
- Risk levels and status fields
- Pricing and financial data
- Consultant assignments

### Data Cleaning
- Fixes common formatting issues
- Standardizes date formats
- Cleans up text fields
- Handles missing values appropriately

### Duplicate Detection
- Identifies potential duplicate properties
- Suggests merge or skip options
- Prevents data duplication

## Tips for Best Results

### File Preparation
1. **Use Clear Column Headers**: Name columns clearly (e.g., "Property Name", "Client Contact")
2. **Consistent Data Format**: Keep dates, prices, and status values consistent
3. **Remove Extra Rows**: Delete any summary rows or notes at the bottom
4. **One Property Per Row**: Each row should represent one property

### Common Column Names the System Recognizes
- **Property**: Property Name, Address, Location, Site Name
- **Client**: Client, Client Name, Macro Client, Company
- **Consultant**: Consultant, Assigned To, Inspector, Agent
- **Dates**: Date, Inspection Date, Scheduled Date, Due Date
- **Status**: Status, Report Status, Contract Status, Stage
- **Risk**: Risk, Crime Risk, Security Risk, Threat Level
- **Price**: Price, Quote, Cost, Amount, Fee

### Email Attachments
If you receive property data via email:
1. Save the email as `.eml` or export from Outlook as `.msg`
2. Upload the email file directly
3. The system will extract and analyze any Excel/CSV attachments

## Troubleshooting

### File Upload Issues
- **File too large**: Maximum size is 100MB
- **Unsupported format**: Ensure file is Excel, CSV, or email format
- **Corrupted file**: Try re-saving or exporting the file

### Data Mapping Issues
- **Columns not recognized**: The system will ask you to manually map unmapped columns
- **Poor confidence scores**: Review suggested mappings and adjust if needed
- **Missing required fields**: Add missing data or use defaults

### Import Errors
- **Duplicate data**: Choose to skip, update, or create new records
- **Invalid dates**: Ensure dates are in recognizable format (MM/DD/YYYY, YYYY-MM-DD, etc.)
- **Missing required fields**: Provide default values or skip incomplete records

## Need Help?

### Download Templates
- Click "Excel Template" or "CSV Template" to download properly formatted examples
- Use these templates to structure your data correctly

### Contact Support
If you encounter issues:
1. Note the exact error message
2. Save a copy of your original file
3. Contact IT support with the details

## Advanced Features

### Bulk Operations
After import, you can:
- Assign consultants to multiple properties
- Update status for multiple records
- Export data in various formats

### Integration
The smart import integrates with:
- Client management system
- Consultant assignment system
- Reporting and analytics tools

---

**Remember**: The Smart Import system is designed to be forgiving and intelligent. Even if your data isn't perfectly formatted, the system will do its best to import it correctly. Always review the preview before final import!
