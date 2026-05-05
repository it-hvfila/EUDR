<?php
namespace App\Console\Commands;

use App\Models\job_it;
use Illuminate\Console\Command;

class AutoCloseJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-close-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('เริ่มปิดงานอัตโนมัติ...');
        $three_days_ago = date('Y-m-d H:i:s', strtotime('-3 days'));
        $jobs = job_it::where('waiting_for_closing', '<', $three_days_ago)->where('status', '=', 4)->get();
        if ($jobs->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'ไม่มีงานที่ต้องปิดอัตโนมัติ']);
        }
        foreach ($jobs as $job) {
            $job->finished_work = date('Y-m-d H:i:s'); // ตอนนี้เลย
            $job->finished_work_name = 'system';
            $job->updated_by = 'system';
            $job->save();
        }
        $this->info("ปิดงานไปทั้งหมด {$jobs->count()} รายการ");
    }
}
