import openpyxl
import sys

# Path to Sheet 1 (update as needed)
SHEET_PATH = r"docs\excel\HB 837 Properties Sheet - 02192025 - (MASTER).xlsx"

wb = openpyxl.load_workbook(SHEET_PATH)
sheet = wb.active

# Get header row (assume first row)
headers = [cell.value for cell in next(sheet.iter_rows(min_row=1, max_row=1))]

print("Sheet 1 Fields:")
for h in headers:
    print(f"- {h}")
