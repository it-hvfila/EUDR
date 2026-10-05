-- Only for an existing database verified to match the 2026-10-05 baseline.
-- This records migration history without recreating tables or changing application rows.
-- Back up first. Do not use for an empty database or one with existing migration history.
CREATE TABLE `migrations` (
    `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `migration` varchar(255) NOT NULL,
    `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
    ('2026_10_05_000000_create_users_table', 1),
    ('2026_10_05_000001_create_customers_table', 1),
    ('2026_10_05_000002_create_document_categories_table', 1),
    ('2026_10_05_000003_create_suppliers_table', 1),
    ('2026_10_05_000004_create_company_documents_table', 1),
    ('2026_10_05_000005_create_supplier_documents_table', 1),
    ('2026_10_05_000006_create_lots_table', 1),
    ('2026_10_05_000007_create_lot_files_table', 1),
    ('2026_10_05_000008_create_compound_lots_links_table', 1),
    ('2026_10_05_000009_create_compound_fg_links_table', 1),
    ('2026_10_05_000010_create_customer_reports_table', 1),
    ('2026_10_05_000011_create_customer_report_files_table', 1),
    ('2026_10_05_000012_create_log_downloads_table', 1);
