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
CREATE TABLE `lot_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lot_id` int unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` decimal(8,2) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('lot_files');
    }
};
