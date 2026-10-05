<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Baseline captured from the live EUDR MySQL 8 schema on 2026-10-05.
return new class extends Migration
{
    public function up(): void
    {
        // Keep native types, collations, defaults and constraints exactly as deployed.
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_report_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `report_id` bigint unsigned NOT NULL,
  `token` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `lot_file_id` int unsigned DEFAULT NULL,
  `supplier_document_id` int unsigned DEFAULT NULL,
  `company_document_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_report_files_token_unique` (`token`),
  UNIQUE KEY `customer_report_files_lot_unique` (`report_id`,`lot_file_id`),
  UNIQUE KEY `customer_report_files_supplier_unique` (`report_id`,`supplier_document_id`),
  UNIQUE KEY `customer_report_files_company_unique` (`report_id`,`company_document_id`),
  KEY `customer_report_files_lot_fk` (`lot_file_id`),
  KEY `customer_report_files_supplier_fk` (`supplier_document_id`),
  KEY `customer_report_files_company_fk` (`company_document_id`),
  CONSTRAINT `customer_report_files_company_fk` FOREIGN KEY (`company_document_id`) REFERENCES `company_documents` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customer_report_files_lot_fk` FOREIGN KEY (`lot_file_id`) REFERENCES `lot_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customer_report_files_report_fk` FOREIGN KEY (`report_id`) REFERENCES `customer_reports` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customer_report_files_supplier_fk` FOREIGN KEY (`supplier_document_id`) REFERENCES `supplier_documents` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customer_report_files_one_source` CHECK (((((`lot_file_id` is not null) + (`supplier_document_id` is not null)) + (`company_document_id` is not null)) = 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_report_files');
    }
};
