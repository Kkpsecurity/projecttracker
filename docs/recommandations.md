# HB837 Backup/Import/Export System Recommendations

**Date:** 2025-06-24

## Recommendations & Issues

1. **ZIP/Postal Code Validation**
   - Update validation logic to support:
     - US ZIP+4 format (e.g., 12345-6789)
     - Alphanumeric postal codes (for Canadian/UK addresses, e.g., K1A 0B1, SW1A 1AA)
   - Ensure this is reflected in both import validation and UI help text.

2. **Import Title Field**
   - Consider enforcing uniqueness or providing a warning if a similar import title already exists in the audit log, to avoid confusion.

3. **Audit Trail Consistency**
   - Ensure all relevant actions (including failed attempts) are consistently logged with user ID, file name, and import title.

4. **User Feedback**
   - After import, display the generated or provided import title in the success message for user clarity.

5. **Security**
   - Continue to restrict admin/debug features to authorized users only.
   - Consider adding rate limiting or additional checks for import/export endpoints to prevent abuse.

6. **Documentation**
   - Keep `progress.md` and `database_structure_report.md` up to date with all changes.
   - Add a section to the user guide explaining the new import title feature and ZIP code validation rules.

7. **Testing**
   - Add/expand unit and feature tests for import title generation, ZIP code validation, and admin-only UI logic.

---

For any new issues or suggestions, append to this file for future tracking.
