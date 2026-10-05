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
CREATE TABLE `customer_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `pack_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `expired_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `company_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `epicor_customer_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_data` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_reports_token_unique` (`token`),
  UNIQUE KEY `customer_reports_company_pack_unique` (`company_code`,`pack_id`),
  UNIQUE KEY `customer_reports_account_unique` (`customer_id`),
  KEY `customer_reports_customer_status` (`customer_id`,`is_active`,`expired_at`),
  KEY `customer_reports_pack_id` (`pack_id`),
  KEY `customer_reports_creator_fk` (`created_by`),
  CONSTRAINT `customer_reports_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `customer_reports_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_reports');
    }
};
