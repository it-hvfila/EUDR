<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\job_it;
use App\Models\news_it;
use App\Models\news_it_images;
use Carbon\Carbon;
use DB;
use DataTables;

use App\Models\WorkOrder;

use App\Services\TelegramNotifyService;

class HomeController extends Controller
{
    protected $telegramNotify;

    public function __construct(TelegramNotifyService $telegramNotify)
    {
        $this->telegramNotify = $telegramNotify;
    }
    //



    public function index(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // Build the query
        $query = WorkOrder::selectRaw(
            "
            work_order.created_by,
            COUNT(CASE WHEN type_work.type_work = 'HardWare' THEN 1 END) as HardWare,
            COUNT(CASE WHEN type_work.type_work = 'ProJect' THEN 1 END) as ProJect,
            COUNT(CASE WHEN type_work.type_work = 'SoftWare' THEN 1 END) as SoftWare,
            COUNT(CASE WHEN type_work.type_work in ('HardWare','ProJect','SoftWare') THEN 1 END) as Total,
            ROUND(AVG(TIMESTAMPDIFF(SECOND, work_order.date_start_real, work_order.date_end_real)), 2) as  AvgTimeDifference
            ",
        )
            ->join('type_work', 'work_order.type_work', '=', 'type_work.id')
            ->where(function ($query) {
                $query->whereNotNull('work_order.date_start_real')->orWhere('work_order.date_end_real', '!=', '');
            })
            // ->where(function ($query) {
            //     $query->whereNotNull('work_order.date_start_real')->orWhere('work_order.date_end_real', '!=', '');
            // })
            ->whereRaw('COALESCE(work_order.date_start_real, "") <= COALESCE(work_order.date_end_real, "")')
            ->whereBetween('work_order.created_at', [$start_date, $end_date]) // Apply date range filter
            ->groupBy('work_order.created_by')
            ->get();

        $data = $query->toArray();

        // จัดลำดับตาม Total โดยไม่ใช้ orderBy ใน SQL
        usort($data, function ($a, $b) {
            return $b['Total'] <=> $a['Total'];
        });

        // กำหนดลำดับให้กับแต่ละรายการ
        foreach ($data as $index => &$record) {
            $rank = $index + 1;

            // กำหนดสีของ trophy ตามลำดับ
            switch ($rank) {
                case 1:
                    $record['rank'] = '<i class="bi bi-trophy-fill text-warning"></i>'; // Gold
                    break;
                case 2:
                    $record['rank'] = '<i class="bi bi-trophy-fill text-secondary"></i>'; // Silver
                    break;
                case 3:
                    $record['rank'] = '<i class="bi bi-trophy-fill text-bronze"></i>'; // Bronze
                    break;
                default:
                    $record['rank'] = $rank; // แสดงลำดับเป็นตัวเลขสำหรับรายการอื่น
            }
        }

        return DataTables::of(collect($data))
            ->addIndexColumn()
            ->rawColumns(['rank'])
            ->toJson();
    }
}
