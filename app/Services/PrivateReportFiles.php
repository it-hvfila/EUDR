<?php

namespace App\Services;


class PrivateReportFiles
{
    public static function path(?string $relative): string
    {
        abort_unless($relative && preg_match('#^uploads/(lots|supplier_doc|company_doc)/[^/\\\\]+$#u', $relative), 404);
        return storage_path('app/private/'.$relative);
    }

    public static function existing(?string $relative): string
    {
        $path = self::path($relative);
        $real = realpath($path);
        $root = realpath(storage_path('app/private'));
        abort_unless($root && $real && str_starts_with($real, $root.DIRECTORY_SEPARATOR) && is_file($real), 404, 'File not found');
        return $real;
    }

    public static function assertUnlinked(string $column, $ids): void
    {
        abort_if(\Illuminate\Support\Facades\DB::connection('mysql2')->table('customer_report_files')
            ->whereIn($column, (array) $ids)->exists(), 409,
            'เอกสารนี้ถูกใช้ในรายงานแล้ว กรุณาเพิ่มเอกสารใหม่เพื่อรักษาหลักฐานเดิม');
    }

}
