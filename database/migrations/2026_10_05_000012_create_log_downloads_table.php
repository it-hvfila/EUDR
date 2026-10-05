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
CREATE TABLE `log_downloads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `topic_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `downloaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `report_id` bigint unsigned DEFAULT NULL,
  `report_file_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_log_customer` (`customer_id`),
  KEY `log_downloads_report_fk` (`report_id`),
  KEY `log_downloads_report_file_fk` (`report_file_id`),
  CONSTRAINT `fk_log_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `log_downloads_report_file_fk` FOREIGN KEY (`report_file_id`) REFERENCES `customer_report_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `log_downloads_report_fk` FOREIGN KEY (`report_id`) REFERENCES `customer_reports` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('log_downloads');
    }
};
