<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The baseline may have been recorded on an older server schema.
        foreach (['report_id' => 'customer_reports', 'report_file_id' => 'customer_report_files'] as $column => $parent) {
            if (! Schema::hasColumn('log_downloads', $column)) {
                Schema::table('log_downloads', function (Blueprint $table) use ($column) {
                    $table->unsignedBigInteger($column)->nullable();
                });
            }

            $constraint = $column === 'report_id'
                ? 'log_downloads_report_fk'
                : 'log_downloads_report_file_fk';

            $foreignKey = DB::selectOne(
                'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                 AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = ?
                 AND REFERENCED_COLUMN_NAME = ?',
                ['log_downloads', $column, $parent, 'id']
            );

            if (! $foreignKey) {
                Schema::table('log_downloads', function (Blueprint $table) use ($column, $parent, $constraint) {
                    $table->foreign($column, $constraint)->references('id')->on($parent)->restrictOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        // These columns may predate this repair, and now contain audit history.
        throw new RuntimeException('This schema repair cannot be rolled back automatically without risking download audit history.');
    }
};
