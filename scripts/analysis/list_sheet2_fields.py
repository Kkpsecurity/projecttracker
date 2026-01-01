import openpyxl

SHEET_PATH = r"docs\excel\HB 837 Properties Sheet - 02252025 - (FOR UPLOAD).xlsx"

wb = openpyxl.load_workbook(SHEET_PATH)
sheet = wb.active

headers = [cell.value for cell in next(sheet.iter_rows(min_row=1, max_row=1))]

print("Sheet 2 Fields:")
for h in headers:
    print(f"- {h}")
