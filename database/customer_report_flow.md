# Customer report download flow

The existing employee session can search Pack ID and click “สร้างรายงานสำหรับลูกค้า”.
Creation uses the server-side search snapshot, expires after 30 days by default,
and atomically creates a temporary customer account, report and evidence links.
A unique `(company_code, pack_id)` key prevents duplicate accounts on repeated or
simultaneous requests. A unique `customer_id` makes each generated account report-specific.
Existing reports keep their original snapshot, account and password.

Passwords are displayed only in the creation/reset POST response (not in the PDF).
Send credentials separately from the report. Resetting a password invalidates
customer sessions via the stored password fingerprint; expired or revoked reports
are not automatically renewed.

Customer links are under `/portal/reports/{report}/files/{file}/download`.
Every request checks active account, credential version, expiry, report owner and
file membership. `log_downloads` records authorized responses, not completed transfers.
Employee download URLs remain available behind `username.session`.

Protected files live under `storage/app/private/uploads/{lots,supplier_doc,company_doc}`.
Database-relative paths remain unchanged. New uploads use private storage.
Files referenced by a report cannot be edited or deleted; add new evidence instead.

## Deploying to another database

1. Back up the database and the evidence directories.
2. Apply `create_customer_reports.sql` once if those tables do not exist.
3. Apply `upgrade_customer_reports.sql` once.
4. Deploy the controllers, services, routes, config and views together.
5. Run `python3 scripts/move_report_files.py` from this project (requires Python 3).
   It backs up the three public directories and verifies SHA-256 before removing
   public copies. Do not restore those directories under `public` after deployment.
6. Clear Laravel route/config/view caches and verify download routes.

The migration was applied to the current Docker database. Existing public evidence
was moved locally with a private tar backup. SQL scripts intentionally fail if
applied again rather than silently accepting incompatible schemas.

Old PDFs containing public file URLs must be regenerated with protected links.
API customer code is read from `Customer_CustID` or `Customer_CustNum`; if the BAQ
omits both it remains null. Company is read from `ShipHead_Company` or
`ShipDtl_Company`, defaulting to the current HVF installation. Multi-company use
requires the BAQ to return company explicitly. Company evidence tokens currently
match the fixed evidence rows in Invoice.blade.php and are listed in
`config/customer_reports.php`.
