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
CREATE TABLE `supplier_documents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned DEFAULT NULL,
  `supplier_id` int unsigned DEFAULT NULL,
  `doc_name` varchar(255) NOT NULL,
  `description` text,
  `file_path` varchar(500) DEFAULT NULL,
  `upload_by` varchar(100) DEFAULT NULL,
  `token` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_documents');
    }
};
