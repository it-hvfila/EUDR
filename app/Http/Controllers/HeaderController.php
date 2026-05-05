<?php

namespace App\Http\Controllers;
use App\Models\job_it;

use App\Models\news_it;
use Carbon\Carbon;
use Illuminate\Http\Request;

use DB;


class HeaderController extends Controller
{
    //
    public function index(Request $request)
    {
        // $username = $request->session()->get('user.name');
        $username = $request->session()->get('user.username');


        // Fetch notifications (limit to 4 for display)
        $notifications_news = news_it::query()->where('news_it.status', 3)->where('news_it.start_of_news', '<=', now())->where('news_it.end_of_news', '>=', now())->orderBy('start_of_news', 'asc')->get();

        $notifications = DB::table('job_it')
        ->join('work_order', 'job_it.job_code', '=', 'work_order.job_code')  // การเชื่อมต่อกับตาราง users
        ->where('job_it.created_by', $username)
        ->where('job_it.status_noti', 0)
        ->whereIn('job_it.status', [2, 3, 4])
        ->where('job_it.status', '<>', 5)
        ->orderBy('job_it.created_at', 'desc')
        ->select('job_it.job_code','job_it.title','job_it.created_at','job_it.status', DB::raw('GROUP_CONCAT(IF(work_order.status_work_order != 6, work_order.created_by, NULL) SEPARATOR ", ") as created_by_work_order'))
        ->groupBy('job_it.job_code', 'job_it.title', 'job_it.created_at', 'job_it.status')
        ->get();

        // dd($notifications);

        $formattedNotifications_News = $notifications_news->map(function ($notification_new) {
            // Determine icon and iconColor based on status_job
            $icon = '';
            $iconColor = '';

            switch ($notification_new->status) {
                case 3:
                    $icon = 'bi-exclamation-circle';
                    $iconColor = 'text-warning';
                    break;
                default:
                    $icon = 'bi-question-circle';
                    $iconColor = 'text-secondary';
                    $message = 'error';
                    break;
            }

            return [
                'icon' => $icon,
                'iconColor' => $iconColor,
                'title' => $notification_new->title,
                'description' => $notification_new->description,
                'time_ago' => Carbon::parse($notification_new->start_of_news)->diffForHumans()
            ];
        });
        $formattedNotifications = $notifications->map(function ($notification) {
            // Determine icon and iconColor based on status_job
            $icon = '';
            $iconColor = '';

            switch ($notification->status) {
                case 2:
                    $icon = 'bi-info-circle';
                    $iconColor = 'text-primary';
                    $message = 'ประเมินงานแล้ว';
                    break;
                case 3:
                    $icon = 'bi-check-circle';
                    $iconColor = 'text-success';
                    $message = 'กำลังดำเนินการ';
                    break;
                case 4:
                    $icon = 'bi-exclamation-circle';
                    $iconColor = 'text-warning';
                    $message = 'กรุณาปิดงาน';
                    break;
                case 6:
                    $icon = ' bi-x-circle';
                    $iconColor = 'text-danger';
                    $message = 'Cancle';
                    break;
                default:
                    $icon = 'bi-question-circle';
                    $iconColor = 'text-secondary';
                    $message = 'error';
                    break;
            }
            $createdAt = Carbon::parse($notification->created_at);
            return [
                'icon' => $icon,
                'iconColor' => $iconColor,
                'job_code' => $notification->job_code,
                'title' => $notification->title . ' (' . $message . ')',
                'time_ago' => $createdAt->diffForHumans() . ' (' . $notification->created_by_work_order . ')',
            ];
        });

        // Return JSON response
        return response()->json([
            'count_news' => $notifications_news->count(),
            'notifications_news' => $formattedNotifications_News,
            'count' => $notifications->count(),
            'notifications' => $formattedNotifications,
        ]);
    }
}
