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
CREATE TABLE `document_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `report_section` varchar(100) DEFAULT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('document_categories');
    }
};
