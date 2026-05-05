<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DatabaseSwitcher
{
    public static function connect($primaryConnection, $backupConnection)
    {
        try {
            DB::connection($primaryConnection)->getPdo();
            return $primaryConnection;
        } catch (\Exception $e) {
            Log::error('Connection to primary database (' . $primaryConnection . ') failed: ' . $e->getMessage());

            Config::set('database.default', $backupConnection);
            DB::purge($primaryConnection);
            DB::reconnect($backupConnection);

            try {
                DB::connection($backupConnection)->getPdo();
                Log::info('Successfully connected to backup database (' . $backupConnection . ').');
                return $backupConnection;
            } catch (\Exception $e) {
                Log::error('Connection to backup database (' . $backupConnection . ') also failed: ' . $e->getMessage());
                throw $e;
            }
        }
    }
}
