import openpyxl

SHEET_PATH = r"docs\\excel\\hb837_projects(10)(3).xlsx"

wb = openpyxl.load_workbook(SHEET_PATH)
sheet = wb.active

headers = [cell.value for cell in next(sheet.iter_rows(min_row=1, max_row=1))]

print("Sheet 3 Fields:")
for h in headers:
    print(f"- {h}")
