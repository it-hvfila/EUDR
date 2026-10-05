-- Apply once after create_customer_reports.sql.
ALTER TABLE customer_reports
    ADD COLUMN company_code VARCHAR(50) NOT NULL DEFAULT '',
    ADD COLUMN epicor_customer_code VARCHAR(100) DEFAULT NULL,
    ADD COLUMN customer_name VARCHAR(100) DEFAULT NULL,
    ADD COLUMN report_data JSON DEFAULT NULL,
    ADD UNIQUE KEY customer_reports_company_pack_unique (company_code, pack_id),
    ADD UNIQUE KEY customer_reports_account_unique (customer_id);
