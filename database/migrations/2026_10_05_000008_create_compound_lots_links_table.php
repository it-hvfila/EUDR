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
CREATE TABLE `compound_lots_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lot_id` int NOT NULL,
  `lot_cpd_no` varchar(100) NOT NULL,
  `remak` varchar(500) DEFAULT NULL,
  `used_weight` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('compound_lots_links');
    }
};
