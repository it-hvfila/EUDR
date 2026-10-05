-- Customer report download permissions. Apply once to the eudr database.
-- Existing traceability records are not changed.
CREATE TABLE customer_reports (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    customer_id INT NOT NULL,
    pack_id VARCHAR(100) NOT NULL,
    invoice_no VARCHAR(100) DEFAULT NULL,
    token VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    expired_at DATETIME NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY customer_reports_token_unique (token),
    KEY customer_reports_customer_status (customer_id, is_active, expired_at),
    KEY customer_reports_pack_id (pack_id),
    CONSTRAINT customer_reports_customer_fk FOREIGN KEY (customer_id)
        REFERENCES customers (id) ON DELETE RESTRICT,
    CONSTRAINT customer_reports_creator_fk FOREIGN KEY (created_by)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customer_report_files (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    report_id BIGINT UNSIGNED NOT NULL,
    token VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    lot_file_id INT UNSIGNED DEFAULT NULL,
    supplier_document_id INT UNSIGNED DEFAULT NULL,
    company_document_id INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY customer_report_files_token_unique (token),
    UNIQUE KEY customer_report_files_lot_unique (report_id, lot_file_id),
    UNIQUE KEY customer_report_files_supplier_unique (report_id, supplier_document_id),
    UNIQUE KEY customer_report_files_company_unique (report_id, company_document_id),
    CONSTRAINT customer_report_files_report_fk FOREIGN KEY (report_id)
        REFERENCES customer_reports (id) ON DELETE RESTRICT,
    CONSTRAINT customer_report_files_lot_fk FOREIGN KEY (lot_file_id)
        REFERENCES lot_files (id) ON DELETE RESTRICT,
    CONSTRAINT customer_report_files_supplier_fk FOREIGN KEY (supplier_document_id)
        REFERENCES supplier_documents (id) ON DELETE RESTRICT,
    CONSTRAINT customer_report_files_company_fk FOREIGN KEY (company_document_id)
        REFERENCES company_documents (id) ON DELETE RESTRICT,
    CONSTRAINT customer_report_files_one_source CHECK (
        (lot_file_id IS NOT NULL) + (supplier_document_id IS NOT NULL)
        + (company_document_id IS NOT NULL) = 1
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nullable so historical download logs remain valid.
ALTER TABLE log_downloads
    ADD COLUMN report_id BIGINT UNSIGNED DEFAULT NULL,
    ADD COLUMN report_file_id BIGINT UNSIGNED DEFAULT NULL,
    ADD CONSTRAINT log_downloads_report_fk FOREIGN KEY (report_id)
        REFERENCES customer_reports (id) ON DELETE RESTRICT,
    ADD CONSTRAINT log_downloads_report_file_fk FOREIGN KEY (report_file_id)
        REFERENCES customer_report_files (id) ON DELETE RESTRICT;

-- The application must generate tokens with a cryptographically secure RNG
-- (e.g. bin2hex(random_bytes(32))) and check customer ownership on every download.
